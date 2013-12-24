<?PHP
namespace Exceptions;

interface BaseExceptionI
{
    /**
     * System is unusable
     */
    const EMERGENCY     = 1;

    /**
     * Immediate action required
     */
    const ALERT         = 2;

    /**
     * Critical conditions
     */
    const CRITICAL      = 3;

    /**
     * Error conditions
     */
    const ERROR         = 4;

    /**
     * Warning conditions
     */
    const WARNING       = 5;

    /**
     * Normal but significant
     */
    const NOTICE        = 6;

    /**
     * Informational
     */
    const INFO          = 7;

    /**
     * 	Debug-level messages
     */
    const DEBUG         = 8;

    /**
     * Mode for raise of exception
     */
    const RISE          = false;
    /**
     * Mode for mute of exception
     */
    const MUTE          = true;
    /**
     * Mode, then function returns exception.
     */
    const RESULT        = 1;

    public function getMessage();
    public function getPrevious();
    public function getCode();
    public function getFile();
    public function getLine();
    public function getTrace();
    public function getTraceAsString();


    /**
     * Template message
     *
     * @return string
     */
    public function template();

    /**
     * The method sets a logging flag.
     *
     * If set flag from TRUE to FALSE,
     * then the exception will not be saved to log (may be).
     *
     * @param   boolean     $flag   logging flag
     *
     * @return  BaseExceptionI
     */
    public function set_loggable($flag);

    /**
     * The method returns a logging flag.
     *
     * TRUE - indicates that an exception is going to be written to the log.
     *
     * @return boolean
     */
    public function is_loggable();

    /**
     * The method returns TRUE - if an exception is fatal.
     *
     * @return boolean
     */
    public function is_fatal();

    /**
     * Method marks the exception as fatal.
     *
     * Calling this method may lead to a call handler fatal errors.
     *
     * @return  BaseException
     */
    public function set_fatal();

    /**
     * The method will return true, if an exception is the container.
     * @return boolean
     */
    public function is_container();

    /**
     * The method returns an error level
     * @return      int
     */
    public function get_level();

    /**
     * The method returns the source of error.
     *
     * The method returns an array of values​​:
     * [
     *      'source'    => class name or file name, where the exception occurred
     *      'type'      => type of the call
     *      'function'  => function or method or closure
     * ]
     *
     * Attention the order of elements in the array is important!
     *
     * @return array
     */
    public function get_source();

    /**
     * The method returns previous exception.
     *
     * It extends the method Exception::getPrevious,
     * and it allows to work with objects which not inherited from Exception class,
     * but they are instances of BaseExceptionI.
     *
     * Also if this exception is container, when that method may be used
     * for getting contained object of BaseExceptionI.
     *
     * @return      BaseExceptionI|\Exception|null
     */
    public function get_previous();

    /**
     * The method returns extra data for exception
     * @return array
     */
    public function get_data();

    /**
     * @param       array       $data   The additional data
     *
     * @return      BaseExceptionI
     */
    public function append_data(array $data);

    /**
     * The method returns debug data for exception
     *
     * @return      array
     */
    public function get_debug_data();

    /**
     * The method serialized object to an array.
     *
     * @return array
     */
    public function to_array();
}