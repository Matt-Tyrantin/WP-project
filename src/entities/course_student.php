<?php
	namespace App\Entities;

	require_once 'entity.php';
	require_once 'course.php';
	require_once 'student.php';

	class CourseStudent extends Entity {
		protected static $entity_name  = 'courses_students';

		protected static $columns = ['id', 'course_id', 'student_id'];

		/**
		 * Returns course this entity is reffering to
		 */
		public function GetCourse()
		{
			return Course::Get($this->GetAttribute('course_id'));
		}

		/**
		 * Returns student this entitiy is reffering to
		 */
		public function GetStudent()
		{
			return Student::Get($this->GetAttribute('student_id'));
		}
	}
?>