Push - Frontend
===============

This is the frontend JavaScript for [**Push**](https://github.com/wbbaddons/Push). It transparently handles
everything that has to be done in order to connect to the push service and provides a nice API for 3rd party developers.

	### Copyright Information
	# @author	Tim Düsterhus
	# @copyright	2012-2015 Tim Düsterhus
	# @license	BSD 3-Clause License <http://opensource.org/licenses/BSD-3-Clause>
	# @package	be.bastelstu.wcf.push
	###

## Code
We start by setting up our environment by ensuring some sane values for both `$` and `window`,
enabling EMCAScript 5 strict mode and overwriting console to prepend the name of the class.

	(($, window) ->
		"use strict";
		
		console =
			log: (message) ->
				window.console.log "[be.bastelstu.wcf.push] #{message}" unless production?
			warn: (message) ->
				window.console.warn "[be.bastelstu.wcf.push] #{message}" unless production?
			error: (message) ->
				window.console.error "[be.bastelstu.wcf.push] #{message}" unless production?

Continue with defining the needed variables. All variables are local to our closure and will be
exposed by a function if necessary.

		initialized = no
		service = null
		connected = no
		events =
			message: { }

Initializes Push with the given service

		init = (_service) ->
			return if initialized
			initialized = yes
			service = _service
			
			service.onConnect -> connected = yes
			service.onDisconnect -> connected = no
			
			for intervalLength in [ 15, 30, 60, 90, 120 ]
				do (intervalLength) ->
					setInterval ->
						return unless connected
						
						message = "be.bastelstu.wcf.push.tick#{intervalLength}"
						return unless events.message[message]?
				
						do events.message[message].fire
					, intervalLength * 1e3
			

Add a new `callback` that will be called when a connection to the push service is established and the
The given `callback` will be called once if a connection is established at time of calling.
Return `true` on success and `false` otherwise.

		onConnect = (callback) ->
			return false unless $.isFunction callback
			
			if initialized
				return service.onConnect callback
			else
				console.warn "Someone tried to bind an onConnect callback before initialization of a service"
				false

Add a new `callback` that will be called when the connection to the push service is lost. Return `true`
on success and `false` otherwise.

		onDisconnect = (callback) ->
			return false unless $.isFunction callback
			
			if initialized
				service.onDisconnect callback
			else
				console.warn "Someone tried to bind an onDisonnect callback before initialization of a service"
				false

Add a new `callback` that will be called when the specified `message` is received. Return `true`
on success and `false` otherwise.

		onMessage = (message, callback) ->
			return false unless $.isFunction callback
			return false unless /^[a-zA-Z0-9-_]+\.[a-zA-Z0-9-_]+(\.[a-zA-Z0-9-_]+)+$/.test message
			
			if initialized
				if /be\.bastelstu\.wcf\.push.tick(15|(3|6|9|12)0)/.test message
					events.message[message] ?= $.Callbacks()
					events.message[message].add callback
					true
				else
					service.onMessage message, callback
			else
				console.warn "Someone tried to bind an onMessage(#{message}) callback before initialization of a service"
				false

And finally export the public methods and variables.

		window.be ?= {}
		be.bastelstu ?= {}
		be.bastelstu.wcf ?= {}
		be.bastelstu.wcf.push = 
			init: init
			onConnect: onConnect
			onDisconnect: onDisconnect
			onMessage: onMessage

	)(jQuery, @)
