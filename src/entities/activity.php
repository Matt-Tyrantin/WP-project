<?php
	namespace App\Entities;

    require_once 'entity.php';
    require_once 'course.php';
    require_once 'activity_student.php';

	class Activity extends Entity {
		protected static $entity_name  = 'activities';

        protected static $columns = ['id', 'name', 'course_id'];

        public static function GetAll($params = [])
        {
            $activities = parent::GetAll($params);

            foreach ($activities as $activity) {
                $activity_students = $activity->GetStudents();

                $activity->SetAttribute('students', $activity_students);
            }

            return $activities;
        }
        
        /**
         * Returns the course which this activity belongs to
         */
        public function GetCourse()
        {
            return Course::Get($this->GetAttribute('course_id'));
        }

        /**
         * Attaches given course to this activity
         */
        public function AttachCourse(Course $course)
        {
            $this->SetAttribute('course_id', $course->GetAttribute(Course::$primary_key));

            foreach ($course->GetStudents() as $student) {
                $activity_student = new ActivityStudent([
                    'activity_id' => $this->GetAttribute(static::$primary_key),
                    'student_id' => $student->GetAttribute(Student::$primary_key)
                ]);

                $activity_student->Save();
            }

            return $this->Save();
        }

        /**
         * Returns all students and their scores for this activity
         */
        public function GetStudents()
        {
            $activity_students = ActivityStudent::GetAll(['activity_id' => $this->GetAttribute(Activity::$primary_key)]);

            return $activity_students;
        }
	}
?>