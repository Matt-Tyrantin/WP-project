<html>
    <head>
        <title>Course Tracker - Course Holders</title>

        <meta charset="UTF-8">

        <?php
            include_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/course_holder_controller.php');

            use \App\Controllers\CourseHolderController as CourseHolderController;

            $controller = new CourseHolderController();

            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $controller->Post($_POST);
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
                <h1>Course Holders</h1>
                <article>
                    <h2>List of holders</h2>
                    <table>
                        <tr>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Courses</th>
                        </tr>

                        <?php
                            $course_holders = $controller->Get();

                            foreach ($course_holders as $course_holder) {
                                echo '<tr>';
                                echo '<td>';
                                echo $course_holder->GetAttribute('first_name');
                                echo '</td>';
                                echo '<td>';
                                echo $course_holder->GetAttribute('last_name');
                                echo '</td>';
                                echo '<td>';
                                $courses = $course_holder->GetCourses();

                                if (count($courses) == 0) {
                                    echo '-';
                                } else {
                                    foreach ($courses as $course) {
                                        echo $course->GetAttribute('name');
                                        echo ', ';
                                    }
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                        ?>
                    </table>
                </article>

                <article>
                    <h2>Add course holder</h2>
                    <form method="POST" action="course_holders.php">
                        <p><label for="first_name">First name: </label><input type="text" name="first_name" required></p>
                        <p><label for="last_name">Last name: </label><input type="text" name="last_name" required></p>

                        <p><input type="submit" value="add"></p>
                    </form>
                </article>
            </section>
        </main>
    </body>
</html>