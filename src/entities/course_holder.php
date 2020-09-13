<?php
	namespace App\Entities;

	require_once 'entity.php';
	require_once 'course.php';

	class CourseHolder extends Entity {
		protected static $entity_name  = 'course_holders';

		protected static $columns = ['id', 'first_name', 'last_name'];

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
		 * Returns full name of the course holder
		 */
		public function GetFullName()
		{
			return $this->GetAttribute('first_name').' '.$this->GetAttribute('last_name');
		}
	}
?>