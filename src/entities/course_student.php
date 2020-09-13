<?php
	namespace App\Entities;

	require_once 'entity.php';

	class CourseStudent extends Entity {
		protected static $entity_name  = 'courses_students';

		protected static $columns = ['id', 'course_id', 'student_id'];
	}
?>