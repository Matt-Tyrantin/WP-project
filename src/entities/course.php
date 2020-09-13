<?php
	namespace App\Entities;

	require_once 'entity.php';
	require_once 'course_holder.php';
	require_once 'activity.php';

	class Course extends Entity {
		protected static $entity_name  = 'courses';

		protected static $columns = ['id', 'name', 'holder_id'];

		/**
		 * Returns the holder of this course
		 */
		public function GetCourseHolder()
		{
			return CourseHolder::GetFromPrimaryKey($this->GetAttribute('holder_id'));
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