<?php
    namespace App\Controllers;
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/controller.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/entities/course_holder.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/entities/course.php');

    use \App\Controllers\Controller as Controller;
    use \App\Entities\CourseHolder as CourseHolder;
    use \App\Entities\Course as Course;

	class CourseController extends Controller {
        /**
         * Returns list of courses with given parameters.
         * 
         * Returns list of all courses if parameters are not given
         */
        public function Get($params = null) 
        {
            if ($params === null) {
                return Course::GetAll();
            } else if (is_array($params)) {
                return Course::GetAll($params);
            } else {
                throw new \Exception("Pramaters GET for Course are of unknown type: ".gettype($params));
            }
        }

        /**
         * Creates a new course with given parameters. Params should be:
         *      'course_name' => name of the course
         *      'course_holder_id' => ID of the course holder to assign this course to. Can be null
         */
        public function Post($params)
        {
            $course = new Course([
                'name' => $params['course_name'],
                'holder_id' => $params['course_holder_id']
            ]);

            return $course->Save();
        }

        /**
         * Updates an existing course with given parameters
         */
        public function Put($params)
        {
            $course = new Course([
                'id' => $params['id'],
                'name' => $params['course_name'],
                'holder_id' => $params['course_holder_id']
            ]);

            return $course->Save();
        }

        /**
         * Removes a specified course
         */
        public function Delete($params)
        {
            $course = Course::Get($params['id']);

            if ($course != null) {
                return $course->Delete();
            } else {
                return true;
            }
        }
	}
?>