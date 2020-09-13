<html>
    <head>
        <title>Course Tracker - Courses</title>

        <meta charset="UTF-8">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

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

        <script>
            $(document).ready(function () {
                $('.edit').click(function() {
                    $.ajax({
                        url     : './api/courses.php',
                        data    : {
                            id : $(this).attr('value')
                        },
                        type    : 'GET',
                        success : function (data) {
                            let attr = JSON.parse(data)[0].attributes;
                            let courses = attr.courses;

                            $('#course-name').text(attr.name);

                            $('#put-form [name=id]').attr('value', attr.id);
                            $('#put-form [name=course_name]').attr('value', attr.name);

                            $('#put-form select').val(attr.holder_id);
                        }
                    });
                });

                $('.remove').click(function() {
                    $.ajax({
                        url     : './api/courses.php',
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

                $('form[class!=activity-show]').submit(function(e) {
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

                $('form.activity-show').submit(function(e) {
                    e.preventDefault();

                    $.ajax({
                        url     : './api/courses.php',
                        data    : {
                            id : $('button[type=submit]', $(this)).attr('value')
                        },
                        type    : 'GET',
                        success : function (data) {
                            let attr = JSON.parse(data)[0].attributes;

                            $('#activity-modal input[name=course_id]').attr('value', attr.id);

                            let table = $('#activity-modal table');

                            table.empty();
                            table.append('<tr>');
                            table.append('<th>Student name</th>');
                            for(activity of attr.activities) {
                                let attr = activity.attributes;

                                table.append('<th><button type="button" class="close remove-activity" value="' + attr.id + '"><span>&times;</span></button>' + attr.name + '</th>');
                            }
                            table.append('</tr>');

                            for(student of attr.students) {
                                let attr = student.attributes;

                                console.log(attr);

                                table.append('<tr>');
                                table.append('<td>' + attr.first_name + ' ' + attr.last_name + '</td>');
                                for(activity of attr.course_activities) {
                                    let attr = activity.attributes;

                                    table.append('<td><input type="number" class="col-sm-6 score-activity" min="0" name="' + attr.id +'" value="' + attr.score + '"></td>');
                                }
                                table.append('</tr>');
                            }
                        }
                    });
                })

                $(document).on('click', 'button.remove-activity', function() {
                    $.ajax({
                        url     : './api/activities.php',
                        data    : {
                            _method : 'DELETE',
                            id : $(this).attr('value')
                        },
                        type    : 'POST',
                        success : function (data) {
                            location.reload();
                        }
                    });
                })

                $(document).on('blur', 'input.score-activity', function () {
                    $.ajax({
                        url     : './api/activities.php',
                        data    : {
                            _method : 'PUT',
                            id      : $(this).attr('name'),
                            score   : $(this).val()
                        },
                        type    : 'POST'
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
                <li class="nav-item"><a class="nav-link" href="./course_holders.php">Course Holders</a></li>
                <li class="nav-item active"><a class="nav-link" href="./courses.php">Courses</a></li>
            </ul>
        </nav>

        <main style="padding-top: 65px;">
            <section class="px-4">
                <h1>Courses</h1><hr>
                <article class="ml-4">
                    <h2>List of courses</h2>
                    <table class="table table-hover">
                        <tr>
                            <th>Edit</th>
                            <th>Name</th>
                            <th>Holder</th>
                            <th>Activites</th>
                            <th>Remove</th>
                        </tr>

                        <?php
                            $courses = $course_controller->Get();

                            foreach ($courses as $course) {
                                $course_id = $course->GetAttribute('id');
                                $course_holder = $course->GetCourseHolder();

                                echo '<tr>';
                                echo '<tr>';
                                echo '<td>';
                                echo "<button type=\"button\" class=\"edit btn btn-primary\" data-toggle=\"modal\" data-target=\"#entity-modal\" value=\"{$course_id}\">Edit</button>";
                                echo '</td>';

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

                                echo '<td>';
                                echo '<form class="activity-show" method="GET">';
                                echo "<button type=\"submit\" class=\"activity btn btn-info\" data-toggle=\"modal\" data-target=\"#activity-modal\" value=\"{$course_id}\">Activities</button>";
                                echo '</form>';
                                echo '</td>';

                                echo '<td>';
                                echo "<button type=\"button\" class=\"remove btn btn-danger\" value=\"{$course_id}\">Remove</button>";
                                echo '</td>';
                                echo '</tr>';
                            }
                        ?>
                    </table>
                </article>

                <article class="ml-4">
                    <h2>Add course</h2><hr>
                    <div class="pl-4">
                        <form class="form-horizontal" id="post-form" method="POST" action="./api/courses.php">
                            <div class="form-group row">
                                <label for="first_name" class="col-sm-1">Course name:</label>
                                <div class="col-sm-4">
                                    <input type="text" name="course_name" class="form-control" placeholder="Enter course name" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="course_holder_id" class="col-sm-1">Holder:</label>
                                <div class="col-sm-4">
                                    <select class="custom-select" name="course_holder_id">
                                        <option selected value="null">&#x2012;</option>
                                        <?php
                                            foreach($holder_controller->Get() as $course_holder) {
                                                $holder_id = $course_holder->GetAttribute('id');
                                                $holder_name = $course_holder->GetFullName();

                                                echo "<option value=\"{$holder_id}\">{$holder_name}</option>";
                                            }
                                        ?>
                                    </select>
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
                            <h1 class="modal-title">Edit course</h1>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </header>

                        <article class="modal-body">
                            <h2 id="course-name">Course name</h2><hr>
                            <div class="pl-4">
                                <form class="form-horizontal" id="put-form" method="POST" action="./api/courses.php">
                                    <input name="_method" type="hidden" value="PUT">
                                    <input type="hidden" name="id">

                                    <div class="form-group row">
                                        <label for="first_name" class="col-sm-2">Course name:</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="course_name" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="course_holder_id" class="col-sm-2">Holder:</label>
                                        <div class="col-sm-9">
                                            <select class="custom-select" name="course_holder_id">
                                                <option value="null">&#x2012;</option>
                                                <?php
                                                    foreach($holder_controller->Get() as $course_holder) {
                                                        $holder_id = $course_holder->GetAttribute('id');
                                                        $holder_name = $course_holder->GetFullName();

                                                        echo "<option value=\"{$holder_id}\">{$holder_name}</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-1">
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

            <div class="modal fade" id="activity-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <section class="modal-content">
                        <header class="modal-header">
                            <h1 class="modal-title">Course activities</h1>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </header>

                        <article class="modal-body">
                            <div class="pl-4">
                                <table class="table table-hover">

                                </table><hr>

                                <form id="activity-add" method="POST" action="./api/activities.php">
                                    <input type="hidden" name="course_id">

                                    <div class="form-group row">
                                        <label for="activity_name" class="col-sm-2">Activity name:</label>
                                        <div class="col-sm-6">
                                            <input type="text" name="activity_name" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-1">
                                            <button type="submit" class="btn btn-primary">Add new activity</button>
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