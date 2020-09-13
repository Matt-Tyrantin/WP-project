<?php
    namespace App\Controllers;
    
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/controller.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/entities/course.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/entities/activity.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/entities/activity_student.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/src/entities/student.php');

    use \App\Controllers\Controller as Controller;
    use \App\Entities\Course as Course;
    use \App\Entities\Activity as Activity;
    use \App\Entities\ActivityStudent as ActivityStudent;
    use \App\Entities\Student as Student;

	class ActivityController extends Controller {
        /**
         * Returns list of activites with given parameters.
         * 
         * Returns list of all activities if parameters are not given
         */
        public function Get($params = null) 
        {
            if ($params === null) {
                return Activity::GetAll();
            } else if (is_array($params)) {
                return Activity::GetAll($params);
            } else {
                throw new \Exception("Pramaters GET for Activity are of unknown type: ".gettype($params));
            }
        }

        /**
         * Creates a new activity with given parameters. Params should be:
         *      'activity_name' => name of the new activity
         *      'course_id' => ID of the course this activity belongs to
         */
        public function Post($params)
        {
            $activity = new Activity([
                'name' => $params['activity_name'],
                'course_id' => $params['course_id']
            ]);

            if (!$activity->Save()) {
                return false;
            }

            return $activity->AttachCourse(Course::Get($params['course_id']));
        }

        /**
         * Updates an activity specific for a student. Sole purpose is of changing the score.
         *      'id' => ID of activity_student entity 
         *      'score' => score of activity to give
         */
        public function Put($params)
        {
            $activity = ActivityStudent::Get($params['id']);

            $activity->SetAttribute('score', $params['score']);

            return $activity->Save();
        }

        /**
         * Removes a specified activity
         */
        public function Delete($params)
        {
            $activity = Activity::Get($params['id']);

            if ($activity != null) {
                return $activity->Delete();
            } else {
                return true;
            }
        }
	}
?>s