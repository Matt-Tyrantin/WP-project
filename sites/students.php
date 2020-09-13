<html>
    <head>
        <title>Course Tracker - Students</title>

        <meta charset="UTF-8">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <?php
            include_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/student_controller.php');
            include_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/course_controller.php');

            use \App\Controllers\StudentController as StudentController;
            use \App\Controllers\CourseController as CourseController;

            $student_controller = new StudentController();
            $course_controller = new CourseController();
        ?>

        <script>
            $(document).ready(function () {
                $('.edit').click(function() {
                    $.ajax({
                        url     : './api/students.php',
                        data    : {
                            id : $(this).attr('value')
                        },
                        type    : 'GET',
                        success : function (data) {
                            let attr = JSON.parse(data)[0].attributes;
                            let courses = attr.courses;

                            $('#student-name').text(attr.first_name + ' ' + attr.last_name);

                            $('#put-form [name=id]').attr('value', attr.id);
                            $('#put-form [name=first_name]').attr('value', attr.first_name);
                            $('#put-form [name=last_name]').attr('value', attr.last_name);

                            $('#put-form input[type=checkbox]').prop('checked', false);

                            for (course of courses) {
                                $('#put-form input[type=checkbox][value=' + course.attributes.id + ']').prop('checked', true);
                            }
                        }
                    });
                });

                $('.remove').click(function() {
                    $.ajax({
                        url     : './api/students.php',
                        data    : {
                            _method : 'DELETE',
                            id : $(this).attr('value')
                        },
                        type    : 'POST',
                        success : function (data) {
                            location.reload();
                        }
                    });
                });

                $('form').submit(function(e) {
                    e.preventDefault();

                    let formData = $(this).serialize();

                    $.ajax({
                        type : $(this).attr('method'),
                        url : $(this).attr('action'),
                        data : formData,
                        success : function (data) {
                            location.reload();
                        }
                    });
                });
            });
        </script>
    </head>

    <body>
        <nav>
            <ul>
                <li><a href="./students.php">Students</a></li>
                <li><a href="./course_holders.php">Course Holders</a></li>
                <li><a href="./courses.php">Courses</a></li>
            </ul>
        </nav>

        <main>
            <section>
                <h1>Students</h1>
                <article>
                    <h2>List of students</h2>
                    <table>
                        <tr>
                            <th>Edit</th>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Courses</th>
                            <th>Remove</th>
                        </tr>

                        <?php
                            $students = $student_controller->Get();

                            foreach ($students as $student) {
                                $studnet_id = $student->GetAttribute('id');

                                echo '<tr>';
                                echo '<td>';
                                echo "<a class=\"edit\" value=\"{$studnet_id}\" href=\"#\">Edit</a>";
                                echo '</td>';

                                echo '<td>';
                                echo $student->GetAttribute('first_name');
                                echo '</td>';

                                echo '<td>';
                                echo $student->GetAttribute('last_name');
                                echo '</td>';

                                echo '<td>';
                                $courses = $student->GetCourses();

                                if (count($courses) == 0) {
                                    echo '-';
                                } else {
                                    foreach ($courses as $course) {
                                        echo $course->GetAttribute('name');
                                        echo ', ';
                                    }
                                }
                                echo '</td>';

                                echo '<td>';
                                echo "<a class=\"remove\" value=\"{$studnet_id}\" href=\"#\">Remove</a>";
                                echo '</td>';
                                echo '</tr>';
                            }
                        ?>
                    </table>
                </article>

                <article>
                    <h2>Add student</h2>
                    <form id="post-form" method="POST" action="./api/students.php">
                        <p><label for="first_name">First name: </label><input type="text" name="first_name" required></p>
                        <p><label for="last_name">Last name: </label><input type="text" name="last_name" required></p>
                        <P><label for="courses[]">Select courses: </label></p>
                        <p>
                            <?php
                                foreach ($course_controller->Get() as $course) {
                                    $course_id = $course->GetAttribute('id');
                                    $course_name = $course->GetAttribute('name');

                                    echo "<p><input type=\"checkbox\" name=\"courses[]\" value=\"{$course_id}\">{$course_name}</p>";
                                }
                            ?>
                        </p>
                        <p><input type="submit" value="Add"></p>
                    </form>
                </article>
            </section>

            <section>
                <h1>Edit student</h1>
                <article>
                    <h2 id="student-name">Student name</h2>
                    <form id="put-form" method="POST" action="./api/students.php">
                        <input name="_method" type="hidden" value="PUT">
                        <input type="hidden" name="id">
                        <p><label for="first_name">First name: </label><input type="text" name="first_name" required></p>
                        <p><label for="last_name">Last name: </label><input type="text" name="last_name" required></p>
                        <P><label for="courses[]">Courses: </label></p>
                        <p>
                            <?php
                                foreach ($course_controller->Get() as $course) {
                                    $course_id = $course->GetAttribute('id');
                                    $course_name = $course->GetAttribute('name');

                                    echo "<p><input type=\"checkbox\" name=\"courses[]\" value=\"{$course_id}\">{$course_name}</p>";
                                }
                            ?>
                        </p>
                        <p><input type="submit" value="Save"></p>
                    </form>
                </article>
            </section>
        </main>
    </body>
</html>