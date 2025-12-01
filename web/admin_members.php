<?php
require_once "auth.php";
require_login();
require_once "config.php";

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Access denied.");
}


if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];


    if ($del_id != $_SESSION['member_id']) {

        // Remove committee memberships for this member
        $stmt = $mysqli->prepare("DELETE FROM committee_members WHERE member_id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->close();

        // Remove votes cast by this member
        $stmt = $mysqli->prepare("DELETE FROM plagiarism_votes WHERE member_id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->close();

        // Remove downloads by this member
        $stmt = $mysqli->prepare("DELETE FROM downloads WHERE member_id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->close();

        //Remove donations made by this member
        $stmt = $mysqli->prepare("DELETE FROM donations WHERE member_id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->close();

        // Remove comments made by this member
        $stmt = $mysqli->prepare("DELETE FROM comments WHERE member_id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->close();

        // If this member is an author, delete their items 
        $author_ids = [];
        $stmt = $mysqli->prepare("SELECT author_id FROM authors WHERE member_id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $author_ids[] = (int)$row['author_id'];
        }
        $stmt->close();

        foreach ($author_ids as $aid) {
            // find items for this author
            $item_ids = [];
            $stmt = $mysqli->prepare("SELECT item_id FROM items WHERE author_id = ?");
            $stmt->bind_param("i", $aid);
            $stmt->execute();
            $ires = $stmt->get_result();
            while ($ir = $ires->fetch_assoc()) {
                $item_ids[] = (int)$ir['item_id'];
            }
            $stmt->close();

            foreach ($item_ids as $iid) {
                // delete item-related stuff
                $stmt = $mysqli->prepare("DELETE FROM downloads WHERE item_id = ?");
                $stmt->bind_param("i", $iid);
                $stmt->execute();
                $stmt->close();

                $stmt = $mysqli->prepare("DELETE FROM donations WHERE item_id = ?");
                $stmt->bind_param("i", $iid);
                $stmt->execute();
                $stmt->close();

                $stmt = $mysqli->prepare("DELETE FROM comments WHERE item_id = ?");
                $stmt->bind_param("i", $iid);
                $stmt->execute();
                $stmt->close();

                $stmt = $mysqli->prepare("DELETE FROM plagiarism_votes WHERE item_id = ?");
                $stmt->bind_param("i", $iid);
                $stmt->execute();
                $stmt->close();

                $stmt = $mysqli->prepare("DELETE FROM items WHERE item_id = ?");
                $stmt->bind_param("i", $iid);
                $stmt->execute();
                $stmt->close();
            }

            // delete the author row
            $stmt = $mysqli->prepare("DELETE FROM authors WHERE author_id = ?");
            $stmt->bind_param("i", $aid);
            $stmt->execute();
            $stmt->close();
        }

        //  delete the member 
        $stmt = $mysqli->prepare("DELETE FROM members WHERE member_id = ?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->close();
    }
}


$result = $mysqli->query("SELECT member_id, name, email, organization, join_date, is_admin FROM members ORDER BY member_id");

include __DIR__.'/header.php';
?>

<h1>Manage Members</h1>

<table border="1" cellpadding="6">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Organization</th>
    <th>Joined</th>
    <th>Admin?</th>
    <th>Delete</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= (int)$row['member_id'] ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['organization']) ?></td>
      <td><?= htmlspecialchars($row['join_date']) ?></td>
      <td><?= $row['is_admin'] ? 'Yes' : 'No' ?></td>
      <td>
        <?php if ($row['member_id'] != $_SESSION['member_id']): ?>
          <a href="admin_members.php?delete_id=<?= (int)$row['member_id'] ?>" onclick="return confirm('Delete this member and all related data?');">Delete</a>
        <?php else: ?>
          (you)
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<p><a href="admin.php">Back to Admin Dashboard</a></p>

<?php include __DIR__.'/footer.php'; ?>
