<?php
require_once('../database/dbhelper.php');
if (!empty($_POST)) {
	if (isset($_POST['action'])) {
		$action = $_POST['action'];

		switch ($action) {
			case 'delete':
				if (isset($_POST['id'])) {
					$id = $_POST['id'];

					$queries = [
						[
							'sql' => "UPDATE product 
									  SET id_category = NULL
									  WHERE id_category = ?",
							'params' => [$id]
						],
						[
							'sql' => "DELETE FROM category WHERE id = ?",
							'params' => [$id]
						]
					];
					

					executeBatch($queries);
				}
				break;
		}
	}
}?>