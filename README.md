# Push

Push is an abstract open source push library for [WoltLab Community Framework](http://github.com/WoltLab/WCF). It provides an easy to use API for both, PHP and JavaScript and allows swapping out the underlying push backend.

## How to use

### On the server
```php
<?php
$pushHandler = \wcf\system\push\PushHandler::getInstance();

// second parameter can contain an integer array with userIDs.
// when leaving it empty, the message will be sent to all connected clients.
$pushHandler->sendMessage('be.bastelstu.wcf.push.hello', array());
?>
```

### On the client
```javascript
require([ 'Bastelstu.be/_Push' ], function (Push) {
	Push
	.onConnect(function () { alert('CONNECT') })
	.catch(function (err) { console.log(err) })

	Push
	.onDisconnect(function () { alert('DISCONNECT') })
	.catch(function (err) { console.log(err) })

	Push
	.onMessage('be.bastelstu.wcf.push.hello', function () { alert('World!') })
	.catch(function (err) { console.log(err) })
})
```

### Writing your own push backend

Have a look at [nodePush](http://github.com/wbbaddons/nodePush)!

License
-------

For licensing information refer to the LICENSE file in this folder.
