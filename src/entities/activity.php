<?php
	namespace App\Entities;

    require_once 'entity.php';
    require_once 'course.php';

	class Activity extends Entity {
		protected static $entity_name  = 'students';

        protected static $columns = ['id', 'name', 'course_id'];
        
        /**
         * Returns the course which this activity belongs to
         */
        public function GetCourse()
        {
            return Course::GetFromPrimaryKey($this->GetAttribute('course_id'));
        }
	}
?>