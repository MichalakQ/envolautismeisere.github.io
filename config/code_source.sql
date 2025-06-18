USE transgourmet;

-- 🔁 Suppression si existent
DROP VIEW IF EXISTS site_commande;
DROP TABLE IF EXISTS site;
DROP TABLE IF EXISTS lieu;

-- ✅ Table LIEU (clé primaire = reference)
CREATE TABLE lieu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reference INT not N,
  nom VARCHAR(100) NOT NULL,
  designation VARCHAR(100) UNIQUE,
  stock INT NOT NULL,
  reste int,
  commande int as (stock - reste) STORED 
) ENGINE=InnoDB;
