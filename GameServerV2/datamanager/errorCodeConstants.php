<?php
/**
 * 
 * This file defines all error constants
 * 0-10 Important
 * 10 - 19 MySQL
 * 20 - 29 Parser
 * 30 - 39 Timer
 */

/**/
define(BAD_LOGIN_ATTEMPT,"5");


/*MySQL related*/
define(MYSQL_CONNECTION_ERROR,"11");
define(MYSQL_SELECT_DB_ERROR,"12");
define(MYSQL_QUERY_ERROR,"13");

/*Parser related*/
define(INVALID_ONLY_NUMBERS_STRING, "21");
define(INVALID_EMAIL, "22");

/*Timer related*/



/*Exception related these are normal errors+1000 ex:
 * MYSQL_QUERY_ERROR_TEST = 1011*/

/*Parser related*/
define(A_PARSER_TEST_HAS_FAILED, "1020");
define(INVALID_ONLY_NUMBERS_STRING_TEST, "1021");

/*Timer related*/
define(TIME_TO_UNIX_TEST_FAILED, "1031");

?>