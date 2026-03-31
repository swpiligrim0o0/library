<?php
// config/Database.php
class Database {
    private $host = "localhost";
    private $db_name = "library_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

// models/User.php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $created_at;
    public $profile;

    public function __construct($db) {
        $this->conn = $db;
    }

    // User va uning profilini olish (One-to-One)
    public function getUserWithProfile($id) {
        $query = "SELECT u.*, p.id as profile_id, p.full_name, p.phone, p.address, p.birth_date 
                  FROM " . $this->table_name . " u
                  LEFT JOIN profiles p ON u.id = p.user_id
                  WHERE u.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->created_at = $row['created_at'];
            
            if($row['profile_id']) {
                $this->profile = [
                    'id' => $row['profile_id'],
                    'user_id' => $row['id'],
                    'full_name' => $row['full_name'],
                    'phone' => $row['phone'],
                    'address' => $row['address'],
                    'birth_date' => $row['birth_date']
                ];
            }
        }
        return $this;
    }

    // Yangi user va profil yaratish (One-to-One)
    public function createWithProfile($data) {
        try {
            $this->conn->beginTransaction();
            
            // User yaratish
            $query = "INSERT INTO " . $this->table_name . " (username, email) VALUES (:username, :email)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $data['username']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->execute();
            
            $user_id = $this->conn->lastInsertId();
            
            // Profil yaratish
            $query = "INSERT INTO profiles (user_id, full_name, phone, address, birth_date) 
                      VALUES (:user_id, :full_name, :phone, :address, :birth_date)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':full_name', $data['full_name']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':birth_date', $data['birth_date']);
            $stmt->execute();
            
            $this->conn->commit();
            return $user_id;
        } catch(Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}

// models/Author.php
class Author {
    private $conn;
    private $table_name = "authors";

    public $id;
    public $name;
    public $email;
    public $bio;
    public $books = [];

    public function __construct($db) {
        $this->conn = $db;
    }

    // Author va uning barcha kitoblarini olish (One-to-Many)
    public function getAuthorWithBooks($id) {
        $query = "SELECT a.*, b.id as book_id, b.title, b.isbn, b.published_year, b.pages 
                  FROM " . $this->table_name . " a
                  LEFT JOIN books b ON a.id = b.author_id
                  WHERE a.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $this->books = [];
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(!isset($this->id)) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->bio = $row['bio'];
            }
            
            if($row['book_id']) {
                $this->books[] = [
                    'id' => $row['book_id'],
                    'title' => $row['title'],
                    'isbn' => $row['isbn'],
                    'published_year' => $row['published_year'],
                    'pages' => $row['pages']
                ];
            }
        }
        
        return $this;
    }

    // Author yaratish
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (name, email, bio) VALUES (:name, :email, :bio)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':bio', $this->bio);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
}

// models/Book.php
class Book {
    private $conn;
    private $table_name = "books";

    public $id;
    public $title;
    public $isbn;
    public $published_year;
    public $pages;
    public $author_id;
    public $author;
    public $categories = [];
    public $current_readers = [];

    public function __construct($db) {
        $this->conn = $db;
    }

    // Kitob va uning muallifini olish (One-to-Many)
    public function getBookWithAuthor($id) {
        $query = "SELECT b.*, a.id as author_id, a.name as author_name, a.email as author_email 
                  FROM " . $this->table_name . " b
                  LEFT JOIN authors a ON b.author_id = a.id
                  WHERE b.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->title = $row['title'];
            $this->isbn = $row['isbn'];
            $this->published_year = $row['published_year'];
            $this->pages = $row['pages'];
            $this->author_id = $row['author_id'];
            
            if($row['author_id']) {
                $this->author = [
                    'id' => $row['author_id'],
                    'name' => $row['author_name'],
                    'email' => $row['author_email']
                ];
            }
        }
        return $this;
    }

    // Kitob va uning kategoriyalarini olish (Many-to-Many)
    public function getBookWithCategories($id) {
        $query = "SELECT b.*, c.id as category_id, c.name as category_name, c.description 
                  FROM " . $this->table_name . " b
                  LEFT JOIN book_categories bc ON b.id = bc.book_id
                  LEFT JOIN categories c ON bc.category_id = c.id
                  WHERE b.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $this->categories = [];
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(!isset($this->id)) {
                $this->id = $row['id'];
                $this->title = $row['title'];
                $this->isbn = $row['isbn'];
                $this->published_year = $row['published_year'];
                $this->pages = $row['pages'];
                $this->author_id = $row['author_id'];
            }
            
            if($row['category_id']) {
                $this->categories[] = [
                    'id' => $row['category_id'],
                    'name' => $row['category_name'],
                    'description' => $row['description']
                ];
            }
        }
        
        return $this;
    }

    // Kitobga kategoriya qo'shish (Many-to-Many)
    public function addCategory($category_id) {
        $query = "INSERT INTO book_categories (book_id, category_id) VALUES (:book_id, :category_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':book_id', $this->id);
        $stmt->bindParam(':category_id', $category_id);
        return $stmt->execute();
    }

    // Kitobni o'qiyotgan readerlarni olish (Many-to-Many)
    public function getCurrentReaders() {
        $query = "SELECT r.*, l.loan_date, l.due_date 
                  FROM readers r
                  JOIN loans l ON r.id = l.reader_id
                  WHERE l.book_id = :book_id AND l.status = 'borrowed'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':book_id', $this->id);
        $stmt->execute();
        
        $this->current_readers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $this->current_readers;
    }
}

// models/Reader.php
class Reader {
    private $conn;
    private $table_name = "readers";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $membership_date;
    public $borrowed_books = [];

    public function __construct($db) {
        $this->conn = $db;
    }

    // Reader va uning olgan kitoblarini olish (Many-to-Many)
    public function getReaderWithBooks($id) {
        $query = "SELECT r.*, b.id as book_id, b.title, b.isbn, l.loan_date, l.due_date, l.return_date, l.status 
                  FROM " . $this->table_name . " r
                  LEFT JOIN loans l ON r.id = l.reader_id
                  LEFT JOIN books b ON l.book_id = b.id
                  WHERE r.id = :id
                  ORDER BY l.loan_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $this->borrowed_books = [];
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(!isset($this->id)) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->email = $row['email'];
                $this->phone = $row['phone'];
                $this->membership_date = $row['membership_date'];
            }
            
            if($row['book_id']) {
                $this->borrowed_books[] = [
                    'book_id' => $row['book_id'],
                    'title' => $row['title'],
                    'isbn' => $row['isbn'],
                    'loan_date' => $row['loan_date'],
                    'due_date' => $row['due_date'],
                    'return_date' => $row['return_date'],
                    'status' => $row['status']
                ];
            }
        }
        
        return $this;
    }

    // Kitob olish (Many-to-Many)
    public function borrowBook($book_id, $days = 14) {
        try {
            $this->conn->beginTransaction();
            
            $loan_date = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime("+$days days"));
            
            $query = "INSERT INTO loans (reader_id, book_id, loan_date, due_date, status) 
                      VALUES (:reader_id, :book_id, :loan_date, :due_date, 'borrowed')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':reader_id', $this->id);
            $stmt->bindParam(':book_id', $book_id);
            $stmt->bindParam(':loan_date', $loan_date);
            $stmt->bindParam(':due_date', $due_date);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch(Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Kitob qaytarish
    public function returnBook($book_id) {
        $query = "UPDATE loans 
                  SET return_date = :return_date, status = 'returned' 
                  WHERE reader_id = :reader_id AND book_id = :book_id AND status = 'borrowed'";
        
        $stmt = $this->conn->prepare($query);
        $return_date = date('Y-m-d');
        $stmt->bindParam(':return_date', $return_date);
        $stmt->bindParam(':reader_id', $this->id);
        $stmt->bindParam(':book_id', $book_id);
        
        return $stmt->execute();
    }
}