<?php
/**
 * Project: Laika MVC Framework
 * Author Name: Showket Ahmed
 * Author Email: riyadhtayf@gmail.com
 */

// Namespace
namespace CBM\ModelHelper;

final class Resource
{
    // Config File Resources
    public static function connection_error():string
    {
        return "<body style=\"margin:0;\">\n<div style=\"height:100vh;position:relative;\">\n<h1 style=\"text-align:center;color:#ef3a3a; position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);margin:0;\">!! Database Connection Error !!</h1>\n</div>\n</body>";
    }
}
