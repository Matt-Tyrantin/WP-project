<?php
    namespace App\Controllers;
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/controller.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/entities/course.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/entities/student.php');

    use \App\Controllers\Controller as Controller;
    use \App\Entities\Course as Course;
    use \App\Entities\Student as Student;

	class StudentController extends Controller {
        /**
         * Returns list of students with given parameters
         * 
         * Returns list of all students if parameters are not given
         */
        public function Get($params = null) 
        {
            if ($params === null) {
                return Student::GetAll();
            } else if (is_array($params)) {
                return Student::GetAll($params);
            } else {
                throw new \Exception("Pramaters GET for Student are of unknown type: ".gettype($params));
            }
        }

        /**
         * Creates a new student with given parameters. Params should be:
         *      'first_name' => first name of the student
         *      'last_name' => last name of the student
         *      'courses' => array of courses to automatically assign the student to
         */
        public function Post($params)
        {
            $student = new Student([
                'first_name' => $params['first_name'],
                'last_name' => $params['last_name']
            ]);

            if (!$student->Save()) {
                return false;
            }

            if (!array_key_exists('courses', $params)) {
                $params['courses'] = array();
            }

            $student->AttachCourses($params['courses']);

            return true;
        }

        /**
         * Updates an existing student with given parameters
         */
        public function Put($params)
        {
            $student = new Student([
                'id' => $params['id'],
                'first_name' => $params['first_name'],
                'last_name' => $params['last_name']
            ]);

            if (!$student->Save()) {
                return false;
            }

            if (!array_key_exists('courses', $params)) {
                $params['courses'] = array();
            }

            $student->AttachCourses($params['courses']);

            return true;
        }

        /**
         * Removes a specified student
         */
        public function Delete($params)
        {
            $student = Student::Get($params['id']);

            if ($student != null) {
                return $student->Delete();
            } else {
                return true;
            }
        }
	}
?>