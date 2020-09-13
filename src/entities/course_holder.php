<?php
	namespace App\Entities;

	require_once 'entity.php';
	require_once 'course.php';

	class CourseHolder extends Entity {
		protected static $entity_name  = 'course_holders';

		protected static $columns = ['id', 'first_name', 'last_name'];

		public static function GetAll($params = [])
		{
			$course_holders = parent::GetAll($params);

			foreach ($course_holders as $course_holder) {
				$course_holder->SetAttribute('courses', $course_holder->GetCourses());
			}

			return $course_holders;
		}

		/*
		* Returns all courses which this course holder is holding
		*/
		public function GetCourses()
		{
			$foreign_key = 'holder_id';

			return Course::GetAll([$foreign_key => $this->GetAttribute(static::$primary_key)]);
		}

		/*
		* Attaches course to this course holder
		*/
		public function AttachCourse(Course $course)
		{
			$foreign_key = 'holder_id';

			$course->SetAttribute($foreign_key, $this->GetAttribute(static::$primary_key));

			return $course->Save();
		}

		/**
		 * Attaches an array of coruses to this course holder
		 */
		public function AttachCourses($courses)
		{
			$this->DetachAllCourses();

			if (is_numeric($courses[0])) {
				foreach ($courses as $course_id) {
					$this->AttachCourse(Course::Get($course_id));
				}

			} else {
				foreach ($courses as $course) {
					$this->AttachCourse($course);
				}
			}

			return true;
		}

		/**
		 * Detaches a course from this course holder
		 */
		public function DetachCourse($course)
		{
			if ($course->GetAttribute('holder_id') == $this->GetAttribute(CourseHolder::$primary_key)) {
				$course->SetAttribute('holder_id', null);
				$course->Save();
			}

			return true;
		}

		/**
		 * Detaches all courses currently attached to this course holder
		 */
		public function DetachAllCourses()
		{
			foreach ($this->GetCourses() as $course) {
				if (!$this->DetachCourse($course)) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Returns full name of the course holder
		 */
		public function GetFullName()
		{
			return $this->GetAttribute('first_name').' '.$this->GetAttribute('last_name');
		}
	}
?>