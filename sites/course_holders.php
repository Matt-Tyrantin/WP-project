<html>
    <head>
        <title>Course Tracker - Course Holders</title>

        <meta charset="UTF-8">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

        <?php
            include_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/course_holder_controller.php');
            include_once($_SERVER['DOCUMENT_ROOT'] . '/src/controllers/course_controller.php');

            use \App\Controllers\CourseHolderController as CourseHolderController;
            use \App\Controllers\CourseController as CourseController;

            $course_holder_controller = new CourseHolderController();
            $course_controller = new CourseController();

            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $course_holder_controller->Post($_POST);
            } 
        ?>

<script>
            $(document).ready(function () {
                $('.edit').click(function() {
                    $.ajax({
                        url     : './api/course_holders.php',
                        data    : {
                            id : $(this).attr('value')
                        },
                        type    : 'GET',
                        success : function (data) {
                            let attr = JSON.parse(data)[0].attributes;
                            let courses = attr.courses;

                            $('#holder-name').text(attr.first_name + ' ' + attr.last_name);

                            console.log(courses);

                            $('#put-form [name=id]').attr('value', attr.id);
                            $('#put-form [name=first_name]').attr('value', attr.first_name);
                            $('#put-form [name=last_name]').attr('value', attr.last_name);

                            $('#put-form input[type=checkbox]').prop('checked', false);

                            for (course of courses) {
                                $('#put-form input[type=checkbox][value=' + course.attributes.id + ']').prop('checked', true).attr('disabled', false);
                            }
                        }
                    });
                });

                $('.remove').click(function() {
                    $.ajax({
                        url     : './api/course_holders.php',
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
        <nav class="navbar navbar-expand-md fixed-top navbar-dark bg-dark">
            <a href="../index.php" class="navbar-brand mb-0 h1">College Administration</a>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="./students.php">Students</a></li>
                <li class="nav-item active"><a class="nav-link" href="./course_holders.php">Course Holders</a></li>
                <li class="nav-item"><a class="nav-link" href="./courses.php">Courses</a></li>
            </ul>
        </nav>

        <main style="padding-top: 65px;">
            <section class="px-4">
                <h1>Course holders</h1><hr>
                <article class="ml-4">
                    <h2>List of holders</h2>
                    <table class="table table-hover">
                        <tr>
                            <th>Edit</th>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>Courses</th>
                            <th>Remove</th>
                        </tr>

                        <?php
                            $course_holders = $course_holder_controller->Get();

                            foreach ($course_holders as $course_holder) {
                                $course_holder_id = $course_holder->GetAttribute('id');

                                echo '<tr>';
                                echo '<td>';
                                echo "<button type=\"button\" class=\"edit btn btn-primary\" data-toggle=\"modal\" data-target=\"#entity-modal\" value=\"{$course_holder_id}\">Edit</button>";
                                echo '</td>';

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
                                    $str = '';
                                    foreach ($courses as $course) {
                                        $str .= $course->GetAttribute('name').', ';
                                    }
                                    echo substr($str, 0, -2);
                                }
                                echo '</td>';

                                echo '<td>';
                                echo "<button type=\"button\" class=\"remove btn btn-danger\" value=\"{$course_holder_id}\">Remove</button>";
                                echo '</td>';
                                echo '</tr>';
                            }
                        ?>
                    </table>
                </article>

                <article class="ml-4">
                    <h2>Add course holder</h2><hr>
                    <div class="pl-4">
                        <form class="form-horizontal" id="post-form" method="POST" action="./api/course_holders.php">
                            <div class="form-group row">
                                <label for="first_name" class="col-sm-1">First name:</label>
                                <div class="col-sm-4">
                                    <input type="text" name="first_name" class="form-control" placeholder="Enter first name" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="last_name" class="col-sm-1">Last name:</label>
                                <div class="col-sm-4">
                                    <input type="text" name="last_name" class="form-control" placeholder="Enter last name" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-1">Select courses:</div>
                                <div class="col-sm-4">
                                    <?php
                                        foreach ($course_controller->Get() as $course) {
                                            $course_id = $course->GetAttribute('id');
                                            $course_name = $course->GetAttribute('name');
                                            $course_course_holder = $course->GetAttribute('holder_id');

                                            echo "<div class=\"form-check\">";
                                            echo "<label class=\"form-check-label\">";
                                            echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"courses[]\" value=\"{$course_id}\"";
                                            if ($course_course_holder != null || $course_course_holder > 0) {
                                                echo " disabled";
                                            }
                                            echo ">";
                                            echo $course_name;
                                            echo "</label>";
                                            echo "</div>";
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-1">
                                    <button type="submit" class="btn btn-primary btn-lg">Submit</button>
                                </div>  
                            </div>
                        </form>
                    </div>
                </article>
            </section>

            <div class="modal fade" id="entity-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <section class="modal-content">
                        <header class="modal-header">
                            <h1 class="modal-title">Edit course holder</h1>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </header>

                        <article class="modal-body">
                            <h2 id="holder-name">Holder name</h2><hr>
                            <div class="pl-4">
                                <form class="form-horizontal" id="put-form" method="POST" action="./api/course_holders.php">
                                    <input name="_method" type="hidden" value="PUT">
                                    <input type="hidden" name="id">
                                    
                                    <div class="form-group row">
                                        <label for="first_name" class="col-sm-2">First name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="first_name" class="form-control" placeholder="Enter first name" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="last_name" class="col-sm-2">Last name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="last_name" class="form-control" placeholder="Enter last name" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-2">Select courses:</div>
                                        <div class="col-sm-9">
                                            <?php
                                                foreach ($course_controller->Get() as $course) {
                                                    $course_id = $course->GetAttribute('id');
                                                    $course_name = $course->GetAttribute('name');
                                                    $course_course_holder = $course->GetAttribute('holder_id');

                                                    echo "<div class=\"form-check\">";
                                                    echo "<label class=\"form-check-label\">";
                                                    echo "<input class=\"form-check-input\" type=\"checkbox\" name=\"courses[]\" value=\"{$course_id}\"";
                                                    if ($course_course_holder != null || $course_course_holder > 0) {
                                                        echo " disabled";
                                                    }
                                                    echo ">";
                                                    echo $course_name;
                                                    echo "</label>";
                                                    echo "</div>";
                                                }
                                            ?>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-2">
                                            <button type="submit" class="btn btn-primary btn-lg">Save</button>
                                        </div>  
                                    </div>
                                </form>
                            </div>
                        </article>

                        <footer class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </footer>
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>