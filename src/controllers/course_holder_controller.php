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
            $course_holder = new CourseHolder([
                'first_name' => $params['first_name'],
                'last_name' => $params['last_name']
            ]);

            if (!$course_holder->Save()) {
                return false;
            }

            if (!array_key_exists('courses', $params)) {
                $params['courses'] = array();
            }

            $course_holder->AttachCourses($params['courses']);

            return true;
        }

        /**
         * Updates an existing course holder with given parameters
         */
        public function Put($params)
        {
            $course_holder = new CourseHolder([
                'id' => $params['id'],
                'first_name' => $params['first_name'],
                'last_name' => $params['last_name']
            ]);

            if (!$course_holder->Save()) {
                return false;
            }

            if (!array_key_exists('courses', $params)) {
                $params['courses'] = array();
            }

            $course_holder->AttachCourses($params['courses']);

            return true;
        }

        /**
         * Removes a specified course holder
         */
        public function Delete($params)
        {
            $course_holder = CourseHolder::Get($params['id']);

            if ($course_holder != null) {
                return $course_holder->Delete();
            } else {
                return true;
            }
        }
	}
?>