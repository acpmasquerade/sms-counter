SMS Counter
=============================

Character counter for SMS messages.


Usage
----------

```javascript
SmsCounter.count('content of the SMS')
```

This will return the following object:

```javascript
{
	encoding: "GSM_7BIT",
	length: 18,
	messages: 1,
	per_message: 160,
	remaining: 142
}
```

jQuery Plugin
----------

Given the following markup:

```html
<textarea name="message" id="message"></textarea>
<ul id="sms-counter">
	<li>Encoding: <span class="encoding"></span></li>
	<li>Length: <span class="length"></span></li>
	<li>Messages: <span class="messages"></span></li>
	<li>Per Message: <span class="per_message"></span></li>
	<li>Remaining: <span class="remaining"></span></li>
</ul>
```

You can use the `countSms` jQuery extension to update the count on keyup. jQuery extension is built-in within the  library.

```javascript
$('#message').countSms('#sms-counter')
```

PHP Class
-----------
SMS Counter is now available as a PHP Class too
```php
<?php
// include the class library
include_once __DIR__.'/sms_counter.php';
// call
$sms_count = SMSCounter::count("some-text");
print_r($sms_count);
```
```html
<pre>
Outputs: 
stdClass Object
(
    [encoding] => GSM_7BIT
    [length] => 9
    [per_message] => 160
    [remaining] => 151
    [messages] => 1
)
</pre>
````

Refer readme-php.md for PHP Class documentation


Known Issue
----

(none)


## License

SMS Counter is released under the [MIT License](LICENSE.txt).
