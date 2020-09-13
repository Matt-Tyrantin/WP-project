<?php
	namespace App\Entities;

	require_once 'entity.php';
	require_once 'course_student.php';
	require_once 'activity_student.php';
	require_once 'activity.php';

	class Student extends Entity {
		protected static $entity_name  = 'students';

		protected static $columns = ['id', 'first_name', 'last_name'];

		public static function GetAll($params = [])
		{
			$students = parent::GetAll($params);

			foreach ($students as $student) {
				$student->SetAttribute('courses', $student->GetCourses());
			}

			return $students;
		}

		/**
		 * Returns all courses this student is attending
		 */
		public function GetCourses()
		{
            $course_students = CourseStudent::GetAll(['student_id' => $this->GetAttribute(static::$primary_key)]);
            $courses = array();

            foreach ($course_students as $course_student) {
                $courses[] = Course::GetFromPrimaryKey($course_student->GetAttribute('course_id'));
            }

            return $courses;
		}
		
		/**
		 * Attaches an array of courses to this student. The array can consist of only numerics which
		 * represent courses' primary key.
		 */
		public function AttachCourses($courses)
		{
			foreach ($this->GetCourses() as $course) {
				$this->DetachCourse($course);
			}

			if (is_numeric($courses[0])) {
				foreach ($courses as $course_id) {
					$this->AttachCourse(Course::Get($course_id));
				}

			} else {
				foreach ($courses as $course) {
					$this->AttachCourse($course);
				}
			}
		}
		
		/**
		 * Attaches a course to this student
		 */
        public function AttachCourse(Course $course)
        {
			$params = [
				'course_id' => $course->GetAttribute(Course::$primary_key),
				'student_id' => $this->GetAttribute(static::$primary_key) 
			];

			if (count(CourseStudent::GetAll($params)) > 0) {
				return true;
			}

            $course_student = new CourseStudent([
				'course_id' => $course->GetAttribute(Course::$primary_key),
				'student_id' => $this->GetAttribute(static::$primary_key) 
			]);

			foreach($course->GetActivities() as $activity) {
				$activity_student = new ActivityStudent([
					'student_id' => $this->GetAttribute(static::$primary_key),
					'activity_id' => $activity->GetAttribute(Activity::$primary_key)
				]);

				$activity_student->Save();
			}

			return $course_student->Save();
		}

		/**
		 * Detaches a course from this student
		 */
		public function DetachCourse(Course $course)
		{
			$params = [
				'course_id' => $course->GetAttribute(Course::$primary_key),
				'student_id' => $this->GetAttribute(static::$primary_key) 
			];

			$course_student = CourseStudent::GetAll($params);

			foreach($course->GetActivities() as $activity) {
				$activity_students = ActivityStudent::GetAll([
					'student_id' => $this->GetAttribute(static::$primary_key)
				]);

				foreacH($activity_students as $activity_student) {
					$activity_student->Delete();
				}
			}

			if (count($course_student) == 0) {
				return true;
			}
			
			return $course_student[0]->Delete();
		}
		
		/**
		 * Returns full name of the student 
		 */
		public function GetFullName()
		{
			return $this->GetAttribute('first_name').' '.$this->GetAttribute('last_name');
		}
	}
?>