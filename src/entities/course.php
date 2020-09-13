<?php
	namespace App\Entities;

	require_once 'entity.php';
	require_once 'course_holder.php';
	require_once 'course_student.php';
	require_once 'activity_student.php';
	require_once 'activity.php';

	class Course extends Entity {
		protected static $entity_name  = 'courses';

		protected static $columns = ['id', 'name', 'holder_id'];

		public static function GetAll($params = [])
		{
			$courses = parent::GetAll($params);

			foreach ($courses as $course) {
				$course_holder = CourseHolder::Get($course->GetAttribute('holder_id'));

				if ($course_holder != null) {
					$course->SetAttribute('course_holder', $course_holder->GetFullName());
				} else {
					$course->SetAttribute('course_holder', null);
				}

				$course->SetAttribute('activities', $course->GetActivites());

				$students = $course->GetStudents();

				foreach ($students as $student) {
					$student_activities = array();

					foreach ($course->GetActivites() as $activity) {
						$activity_student = ActivityStudent::GetAll([
							'student_id' => $student->GetAttribute(Student::$primary_key),
							'activity_id' => $activity->GetAttribute(Activity::$primary_key)
						]);

						$student_activities[] = $activity_student[0];
					}

					$student->SetAttribute('course_activities', $student_activities);
				}

				$course->SetAttribute('students', $students);
			}

			return $courses;
		}

		/**
		 * Returns array of all students attending this course
		 */
		public function GetStudents()
		{
			$course_students = CourseStudent::GetAll(['course_id' => $this->GetAttribute(static::$primary_key)]);

			$students = array();

			foreach ($course_students as $course_student) {
				$students[] = $course_student->GetStudent();
			}

			return $students;
		}

		/**
		 * Returns array of all activities for this course
		 */
		public function GetActivites()
		{
			$activities = Activity::GetAll(['course_id' => $this->GetAttribute(static::$primary_key)]);

			return $activities;
		}

		/**
		 * Returns the holder of this course
		 */
		public function GetCourseHolder()
		{
			return CourseHolder::GetFromPrimaryKey($this->GetAttribute('holder_id'));
		}

		/*
		* Attaches course holder to this course
		*/
		public function AttachCourse(CourseHolder $course_holder)
		{
			$foreign_key = 'holder_id';

			$this->SetAttribute($foreign_key, $course_holder->GetAttribute(static::$primary_key));

			return $this->Save();
		}

		/*
		* Returns all acitivites which this course has
		*/
		public function GetActivities()
		{
			$foreign_key = 'course_id';

			return Activity::GetAll([$foreign_key => $this->GetAttribute(static::$primary_key)]);
		}

		/**
		 * Attaches an activity to this course
		 */
		public function AttachActivity(Activity $activity)
		{
			$foreign_key = 'course_id';

			$activity->SetAttribute($foreign_key, $this->GetAttribute(static::$primary_key));

			return $activity->Save();
		}
	}
?>