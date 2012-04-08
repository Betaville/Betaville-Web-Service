<?php
/**  
 *  Betaville Web Service - A service for accessing data from a Betaville server via HTTP requests
 *  Copyright (C) 2011-2012 Betaville
 *  
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Database Connection Information
//Testing changes to see if new repos working
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'betaville');

define('BETAVILLE_FILE_STORE_URL', 'http://localhost/storage');
define('SERVICE_URL', 'http://localhost/service/service.php');

// Set the default timezone
date_default_timezone_set('America/New_York');
?>
