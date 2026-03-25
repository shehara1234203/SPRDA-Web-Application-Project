<?php include __DIR__ . '/config/header.php';?>

<?php

 if (isset($_POST['add_project'])) {
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $status = $_POST['status'] ?? '';
        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;

        $image_path = uploadImage('image', 'add_project') ?? '';

        $stmt = $conn->prepare("INSERT INTO projects (name, location, status, image_path, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $location, $status, $image_path, $start_date, $end_date);
        $stmt->execute();
        $stmt->close();

        header("Location: dashboard.php");
        exit;
    }

    if (isset($_POST['delete_project'])) {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        header("Location: dashboard.php");
        exit;
    }

    if (isset($_POST['update_project'])) {
        $id = (int)($_POST['id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $status = $_POST['status'] ?? '';
        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;
        $current_image = $_POST['current_image'] ?? '';

        $newImage = uploadImage('image', 'add_project');
        $image_path = $newImage ?? $current_image;

        $stmt = $conn->prepare("UPDATE projects SET name = ?, location = ?, status = ?, image_path = ?, start_date = ?, end_date = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $name, $location, $status, $image_path, $start_date, $end_date, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: dashboard.php");
        exit;
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPRDA - Home</title>

     <!-- Include favicon -->
    <?php include BASE_PATH . '/config/icon.php'; ?>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./Style/index.css">
  
</head>
<body>

<?php include __DIR__ . '/config/carousel.php'; ?>

<?php include __DIR__ . '/public/newstricker.php'; ?>

    <!-- Services Section -->
        <section class="services-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-road"></i>
                            </div>
                            <h6 class="fw-bold"><a href="./public/road&bridge.php"
                                    style="text-decoration:none; color:inherit;">ROAD & Bridges</a></h6>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                            </div>
                          <h6 class="fw-bold"><a href="./public/finance.php"
                                    style="text-decoration:none; color:inherit;">Finance & Administration</a></h6>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="./public/MechanicalWorkshop.php" style="text-decoration:none; color:inherit;">
                            <div class="service-card">
                                <div class="service-icon">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <h6 class="fw-bold"><a href="./public/MechanicalWorkshop.php"
                                        style="text-decoration:none; color:inherit;">Mechanical Workshop</a></h6>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-flask"></i>
                            </div>
                            <h6 class="fw-bold"><a href="./public/matriallab.php"
                                    style="text-decoration:none; color:inherit;">Material Lab Galle</a></h6>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-vial"></i>
                            </div>
                            <h6 class="fw-bold"><a href="./public/matrialweligama.php"
                                    style="text-decoration:none; color:inherit;">Material Lab Weligama</a></h6>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-route"></i>
                            </div>
                            <h6 class="fw-bold"><a href="./public/tourism.php"
                                    style="text-decoration:none; color:inherit;">Tourism Trails</a></h6>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
            </div>
        </section>

        <!-- Main Content Section -->
        <section class="projects-section">
            <div class="container">
                <div class="row">

                    <!-- Recent Projects -->
                     
                    <div class="col-lg-8">
                        <h2 class="mb-4 fw-bold">Recent Projects</h2>
                        <div class="row g-4">
                            <?php
                            // Fetch recent projects from database and render cards
                            // Assumption: projects table has id, name, image_path, status columns
                            if (!isset($conn)) {
                                // ensure database connection
                                require_once __DIR__ . '/includes/db_connect.php';    
                            }

                            // Helper to resolve image paths for this index file. Define it
                            // early so project image paths uploaded via the dashboard
                            // are correctly mapped for frontend rendering.
                            if (!function_exists('resolveWebPathForIndex')) {
                                function resolveWebPathForIndex($p) {
                                    $p = trim((string)$p);
                                    if ($p === '') return '';
                                    if (preg_match('#^https?://#i', $p)) return $p;
                                    if (substr($p,0,1) === '/') return $p;
                                    $rel = ltrim($p, '/');
                                    // public location
                                    $publicFs = __DIR__ . '/public/' . $rel;
                                    if (file_exists($publicFs)) return 'public/' . $rel;
                                    // admin uploads
                                    $adminFs = __DIR__ . '/admin/' . $rel;
                                    if (file_exists($adminFs)) return 'admin/' . $rel;
                                    // root images
                                    $rootFs = __DIR__ . '/' . $rel;
                                    if (file_exists($rootFs)) return $rel;
                                    // fallback to public
                                    return 'public/' . $rel;
                                }
                            }

                            $limit = 6;
                            if (isset($conn)) {
                                $stmt = $conn->prepare("SELECT id, name, image_path, COALESCE(status, '') AS status FROM projects ORDER BY id DESC LIMIT ?");
                                $stmt->bind_param('i', $limit);
                                $stmt->execute();
                                $res = $stmt->get_result();
                                while ($row = $res->fetch_assoc()) {
                                    $pid = (int)$row['id'];
                                    $pname = htmlspecialchars($row['name']);
                                    $pimg = !empty($row['image_path']) ? (function_exists('resolveWebPathForIndex') ? resolveWebPathForIndex($row['image_path']) : $row['image_path']) : 'images/overview.webp';
                                    $pstatus = htmlspecialchars($row['status']);
                                    // choose an icon (fallback to road icon)
                                    $icon = 'fas fa-road';
                                    echo "<div class=\"col-md-4\">\n";
                                    echo "  <div class=\"project-card\">\n";
                                    echo "    <div class=\"project-image\" style=\"background: url('" . $pimg . "'); background-size: cover; background-position: center; position: relative;\">\n";
                                    
                                    echo "      <div style=\"position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.3); color: white; padding: 5px 10px; border-radius: 5px; font-size: 0.8em;\">" . $pstatus . "</div>\n";
                                    echo "    </div>\n";
                                    echo "    <div class=\"p-3\">\n";
                                    echo "      <h6 class=\"fw-bold\">" . $pname . "</h6>\n";
                                    echo "      <a href=\"public\project.php?id=" . $pid . "\" class=\"btn btn-dark\">Read More</a>\n";
                                    echo "    </div>\n";
                                    echo "  </div>\n";
                                    echo "</div>\n";
                                }
                                $stmt->close();
                            } else {
                                // fallback: show a single placeholder card
                                echo '<div class="col-md-4">\n';
                                echo '  <div class="project-card">\n';
                                echo '    <div class="project-image" style="background: url(\'images/overview.webp\'); background-size: cover; background-position: center; position: relative;">\n';
                                echo '      <i class="fas fa-road fa-2x text-white" style="position: absolute; top: 20px; left: 20px;"></i>\n';
                                echo '      <div style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.3); color: white; padding: 5px 10px; border-radius: 5px; font-size: 0.8em;">Road</div>\n';
                                echo '    </div>\n';
                                echo '    <div class="p-3">\n';
                                echo '      <h6 class="fw-bold">No projects available</h6>\n';
                                echo '    </div>\n';
                                echo '  </div>\n';
                                echo '</div>\n';
                            }
                            ?>
                        </div>
                    </div>


                    <!-- News & Events Sidebar -->
<div class="col-lg-4">
    <div class="news-sidebar">
        <h4 class="fw-bold mb-4">
            <a href="./public/newspage.php" style="text-decoration: none; color: inherit;">News & Events</a>
        </h4>

        <?php
        // Ensure database connection (reuse from projects section)
        if (!isset($conn)) {
            require_once __DIR__ . '/../includes/db_connect.php';
        }

        

        $news_limit = 6; // Adjust as needed
        if (isset($conn)) {
            // Prepare and execute query for latest news
            $stmt = $conn->prepare("SELECT id, title, description, image_path, created_at 
                                  FROM news_events 
                                  ORDER BY created_at DESC, id DESC 
                                  LIMIT ?");
            $stmt->bind_param('i', $news_limit);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                echo '<div class="row g-3">'; // Grid layout with gap
                while ($row = $res->fetch_assoc()) {
                    $title = htmlspecialchars($row['title']);
                    $description = !empty($row['description']) ? htmlspecialchars(substr($row['description'], 0, 100)) . '...' : '';
                    $date = '';
                    if (!empty($row['created_at'])) {
                        $date = date('F d, Y', strtotime($row['created_at']));
                    }
                    $img = !empty($row['image_path']) ? resolveWebPathForIndex($row['image_path']) : 'public/images/default-news.jpg';

                    echo '<div class="col-12">'; // Full width cards
                    echo '<div class="card news-card h-100 shadow-sm">'; // Equal height cards
                    echo '<div class="row g-0 align-items-center">';
                    echo '<div class="col-auto">';
                    // echo '<img src="' . htmlspecialchars($img) . '" alt="' . $title . '" style="width:120px; height:80px; object-fit:cover; display:block;">';
                    echo '</div>';
                    echo '<div class="col">';
                    echo '<div class="card-body py-2">';
                    echo '<h6 class="card-title mb-1" style="font-size: 0.9rem;">' . $title . '</h6>';
                    if ($description) {
                        echo '<p class="card-text small mb-2">' . $description . '</p>';
                    }
                    echo '<p class="card-text mb-0"><small class="text-muted">' . $date . '</small></p>';
                    echo '<a href="./public/newsdetails.php?id=' . $row['id'] . '" class="stretched-link"></a>';
                    echo '</div>'; // card-body
                    echo '</div>'; // col
                    echo '</div>'; // row
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                // Fallback if no news
                echo '<div class="news-item">';
                echo '<h6 class="fw-bold">No news available</h6>';
                echo '</div>';
            }

            $stmt->close();
        } else {
            // Fallback if connection fails (similar to projects)
            echo '<div class="news-item">';
            echo '<h6 class="fw-bold">No news available</h6>';
            echo '</div>';
        }
        ?>

        <div class="text-center mt-3">
            <a href="./public/newspage.php" class="btn btn-secondary">More News</a>
        </div>
    </div>
</div>
     </div>
        </div>
            </section>

<?php include __DIR__ . '/config/footer.php'; ?>
       
      
        <script>
        // Add smooth scrolling and interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Add animation on scroll for service cards
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
                    }
                });
            }, observerOptions);

            // Observe service cards and project cards
            document.querySelectorAll('.service-card, .project-card').forEach(card => {
                observer.observe(card);
            });

            // Search functionality
            const searchInput = document.querySelector('.search-box');
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    alert('Search functionality would be implemented here for: ' + this.value);
                }
            });
        });

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .service-card,
            .project-card {
                opacity: 0;
            }
        `;
        document.head.appendChild(style);
                // Reveal project cards with CSS class when they enter viewport
            (function() {
                document.addEventListener('DOMContentLoaded', function() {
                    const cards = document.querySelectorAll('.projects-section .project-card');
                    if (!cards.length) return;
                    if (!('IntersectionObserver' in window)) {
                        cards.forEach(c => c.classList.add('in-view'));
                        return;
                    }

                    const io = new IntersectionObserver((entries, obs) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const el = entry.target;
                                el.classList.add('in-view');
                                obs.unobserve(el);
                            }
                        });
                    }, {
                        threshold: 0.15
                    });

                    cards.forEach((c, i) => {
                        c.classList.add('reveal');
                        if (i % 3 === 0) c.classList.add('delay-1');
                        else if (i % 3 === 1) c.classList.add('delay-2');
                        else c.classList.add('delay-3');
                        io.observe(c);
                    });
                });
            })();
        </script>
       

</body>

</html>