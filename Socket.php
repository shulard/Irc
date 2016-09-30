<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2016, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Hoa\Irc;

use Hoa\Socket as HoaSocket;

/**
 * Class \Hoa\Irc\Socket.
 *
 * Irc specific socket extension.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Socket extends HoaSocket
{
    /**
     * Constructor
     *
     * @param string  $uri      Socket URI
     * @param boolean $secured  Secure mode
     * @param string  $endPoint Websocket endpoint
     */
    public function __construct($uri, $secured = false)
    {
        parent::__construct($uri);

        $this->_secured  = $secured;

        return;
    }

    /**
     * Factory to create a valid instance from the given URI
     *
     * @param string $socketUri URI of the socket to connect to.
     * @return void
     */
    public static function transportFactory($socketUri)
    {
        $parsed = parse_url($socketUri);
        if (false === $parsed) {
            throw new Exception(
                'URL %s seems syntactically invalid.',
                0,
                $socketUri
            );
        }

        $secured = isset($parsed['scheme'])?
            'ircs' === $parsed['scheme']:
            false;

        if (isset($parsed['port'])) {
            $port = $parsed['port'];
        } else {
            /**
             * https://tools.ietf.org/html/draft-butcher-irc-url-04#section-2.4
             * Regarding RFC, port 194 is likely to be a more "authentic"
             * server, however at this time the majority of IRC non secure
             * servers are available on port 6667, at least.
             */
            $port = true === $secured ? 994 : 6667;
        }

        return new static(
            'tcp://' . $parsed['host'] . ':' . $port,
            $secured
        );
    }
}

/**
 * Register socket wrappers
 */
HoaSocket\Transport::register('irc', ['Hoa\Irc\Socket', 'transportFactory']);
HoaSocket\Transport::register('ircs', ['Hoa\Irc\Socket', 'transportFactory']);
