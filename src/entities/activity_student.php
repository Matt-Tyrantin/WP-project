<?php
	namespace App\Entities;

    require_once 'entity.php';
    require_once 'course.php';

	class ActivityStudent extends Entity {
		protected static $entity_name  = 'activities_students';

        protected static $columns = ['id', 'activity_id', 'student_id', 'score'];
	}
?>