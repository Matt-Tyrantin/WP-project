<html>
    <head>
        <title>Course Tracker - Courses</title>

        <meta charset="UTF-8">

        <?php
            include_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/course_controller.php');
            include_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/course_holder_controller.php');

            use \App\Controllers\CourseController as CourseController;
            use \App\Controllers\CourseHolderController as CourseHolderController;

            $course_controller = new CourseController();
            $holder_controller = new CourseHolderController();

            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $course_controller->Post($_POST);
            } 
        ?>
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
                <h1>Courses</h1>
                <article>
                    <h2>List of courses</h2>
                    <table>
                        <tr>
                            <th>Name</th>
                            <th>Holder</th>
                        </tr>

                        <?php
                            $courses = $course_controller->Get();

                            foreach ($courses as $course) {
                                $course_holder = $course->GetCourseHolder();

                                echo '<tr>';
                                echo '<td>';
                                echo $course->GetAttribute('name');
                                echo '</td>';
                                echo '<td>';
                                if ($course_holder != null) {
                                    echo $course_holder->GetFullName();
                                } else {
                                    echo '-';
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                        ?>
                    </table>
                </article>

                <article>
                    <h2>Add course holder</h2>
                    <form method="POST" action="courses.php">
                        <p><label for="name">Course name: </label><input type="text" name="name" required></p>
                        <p><label for="course_holder_id">Holder: </label><select name="course_holder_id">
                            <option value="null">-</option>
                            <?php
                                foreach($holder_controller->Get() as $course_holder) {
                                    $holder_id = $course_holder->GetAttribute('id');
                                    $holder_name = $course_holder->GetFullName();

                                    echo "<option value=\"{$holder_id}\">{$holder_name}</option>";
                                }
                            ?>
                        </select></p>

                        <p><input type="submit" value="add"></p>
                    </form>
                </article>
            </section>
        </main>
    </body>
</html>