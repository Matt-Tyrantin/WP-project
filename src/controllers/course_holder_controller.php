<?php
    namespace App\Controllers;
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/controller.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/entities/course_holder.php');

    use \App\Controllers\Controller as Controller;
    use \App\Entities\CourseHolder as CourseHolder;

	class CourseHolderController extends Controller {
        /**
         * Returns list of course holders with given parameters or a single course holder 
         * if parameter is primary key.
         * 
         * Returns list of all course holders if parameters are not given
         */
        public function Get($params = null) 
        {
            if ($params === null) {
                return CourseHolder::GetAll();
            } else if (is_numeric($params)) {
                return CourseHolder::Get($params);
            } else if (is_array($params)) {
                return CourseHolder::GetAll($params);
            } else {
                throw new \Exception("Pramaters GET for CourseHolder are of unknown type: ".gettype($params));
            }
        }

        /**
         * Creates a new course holder with given parameters. Params should be:
         *      'first_name' => first name of the holder
         *      'last_name' => last name of the holder
         */
        public function Post($params)
        {
            $course_holder = new CourseHolder($params);

            return $course_holder->Save();
        }

        /**
         * Updates an existing course holder with given parameters
         */
        public function Put($params)
        {

        }
	}
?>