<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];

$sql = "SELECT c.connectionID, c.Name, c.RelationshipType, c.Birthday, c.Bio, s.stringHealth 
        FROM CONNECTIONS c 
        LEFT JOIN STRINGS s ON c.connectionID = s.connectionID 
        WHERE c.ownerID = ? 
        ORDER BY c.Name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Connections | HoldAString</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #1e1412; 
            color: #f5ebe0; 
            margin: 0; 
            padding: 40px; 
        }
        
        .header-area { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            max-width: 1000px; 
            margin: 0 auto 30px auto; 
        }
        
        h1 { color: #d4a373; margin: 0; }
        
        .btn-add { 
            background: #d4a373; 
            color: #1e1412; 
            padding: 10px 20px; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: bold; 
        }

        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
            gap: 25px; 
            max-width: 1000px; 
            margin: 0 auto; 
        }

        .card { 
            background: #3d2b1f; 
            padding: 25px; 
            border-radius: 15px; 
            border: 1px solid rgba(255, 255, 255, 0.05); 
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
        }
        
        .card:hover { transform: translateY(-5px); border-color: #d4a373; }

        .type-tag { 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            background: rgba(212, 163, 115, 0.2); 
            color: #d4a373; 
            padding: 4px 8px; 
            border-radius: 4px; 
            display: inline-block;
            margin-bottom: 10px;
        }

        .name { font-size: 1.4rem; margin: 0 0 10px 0; color: #fff; }
        
        .bio { font-size: 0.9rem; opacity: 0.8; margin-bottom: 15px; line-height: 1.4; flex-grow: 1; }

        .meta-info { font-size: 0.8rem; color: #ccd5ae; margin-bottom: 15px; }

        .health-bar-container { 
            background: #2d1f18; 
            height: 8px; 
            border-radius: 4px; 
            margin-bottom: 20px; 
            overflow: hidden;
        }
        
        .health-fill { 
            height: 100%; 
            background: #d4a373; 
            border-radius: 4px; 
        }

        .actions { 
            display: flex; 
            justify-content: space-between; 
            border-top: 1px solid rgba(255,255,255,0.1); 
            padding-top: 15px; 
        }
        
        .actions a { text-decoration: none; font-size: 0.85rem; font-weight: bold; }
        .edit { color: #d4a373; }
        .delete { color: #ff6b6b; }

        .empty-state { text-align: center; margin-top: 100px; opacity: 0.5; }
    </style>
</head>
<body>

<div class="header-area">
    <h1>Your Strings</h1>
    <div>
        <a href="dashboard.php" style="color: #d4a373; margin-right: 20px; text-decoration: none;">Dashboard</a>
        <a href="add_connection.php" class="btn-add">+ New Connection</a>
    </div>
</div>

<div class="grid">
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="card">
                <div>
                    <span class="type-tag"><?php echo htmlspecialchars($row['RelationshipType']); ?></span>
                    <h3 class="name"><?php echo htmlspecialchars($row['Name']); ?></h3>
                </div>

                <p class="bio">
                    <?php echo !empty($row['Bio']) ? htmlspecialchars($row['Bio']) : "<i>No bio added yet.</i>"; ?>
                </p>

                <div class="meta-info">
                    <?php if(!empty($row['Birthday'])): ?>
                        📅 Birthday: <?php echo date("M d, Y", strtotime($row['Birthday'])); ?>
                    <?php endif; ?>
                </div>

                <div class="health-bar-container">
                    <?php $health = $row['stringHealth'] ?? 100; ?>
                    <div class="health-fill" style="width: <?php echo $health; ?>%;"></div>
                </div>
                <div style="font-size: 0.7rem; margin-top: -15px; margin-bottom: 15px; opacity: 0.6;">
                    String Health: <?php echo $health; ?>%
                </div>

                <div class="actions">
                    <a href="edit_connection.php?id=<?php echo $row['connectionID']; ?>" class="edit">Edit Details</a>
                    <a href="delete_connection.php?id=<?php echo $row['connectionID']; ?>" 
                       class="delete" 
                       onclick="return confirm('Are you sure you want to delete this string?')">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>No connections found. Start by adding a new string!</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>