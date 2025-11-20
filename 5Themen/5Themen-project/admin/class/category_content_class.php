<?php
class CategoryContent
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getByCategory($catId)
    {
        $sql = "SELECT * FROM tbl_category_content WHERE category_id = '$catId' LIMIT 1";
        return $this->db->select($sql);
    }

    public function insert($catId, $title, $content)
    {
        $catId = mysqli_real_escape_string($this->db->link, $catId);
        $title = mysqli_real_escape_string($this->db->link, $title);
        $content = mysqli_real_escape_string($this->db->link, $content);

        $sql = "INSERT INTO tbl_category_content (category_id, title, content)
                VALUES ('$catId', '$title', '$content')";
                
        return $this->db->insert($sql);
    }

    public function update($id, $title, $content)
    {
        $id = mysqli_real_escape_string($this->db->link, $id);
        $title = mysqli_real_escape_string($this->db->link, $title);
        $content = mysqli_real_escape_string($this->db->link, $content);

        $sql = "UPDATE tbl_category_content 
                SET title = '$title', content = '$content'
                WHERE id = '$id'";

        return $this->db->update($sql);
    }

    public function getAll()
    {
        return $this->db->select("SELECT cc.*, c.category_name 
                                  FROM tbl_category_content cc
                                  JOIN tbl_category c ON cc.category_id = c.category_id");
    }

    public function getById($id)
    {
        return $this->db->select("SELECT * FROM tbl_category_content WHERE id = '$id'");
    }

    public function delete($id)
    {
        return $this->db->delete("DELETE FROM tbl_category_content WHERE id = '$id'");
    }
}
?>
