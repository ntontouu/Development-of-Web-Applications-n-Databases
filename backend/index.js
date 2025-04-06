const express = require('express');
const mysql = require('mysql');
const cors = require('cors');

const app = express();
const PORT = 5000;

app.use(cors());
app.use(express.json());

// Σύνδεση με τη βάση δεδομένων MySQL
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root', // Βάλε εδώ το username της MySQL
  password: '', // Βάλε εδώ το password της MySQL
  database: 'swimming_app', // Το όνομα της βάσης δεδομένων που θα δημιουργήσεις
});

db.connect((err) => {
  if (err) {
    console.error('Σφάλμα σύνδεσης στη βάση δεδομένων:', err);
  } else {
    console.log('Σύνδεση με τη βάση δεδομένων επιτυχής!');
  }
});

// GET Endpoint για εμφάνιση χρηστών
app.get('/users', (req, res) => {
  const query = 'SELECT * FROM users';
  db.query(query, (err, results) => {
    if (err) {
      return res.status(500).send(err);
    }
    res.json(results);
  });
});

// POST Endpoint για προσθήκη χρήστη
app.post('/users', (req, res) => {
  const { name } = req.body;
  const query = 'INSERT INTO users (name) VALUES (?)';
  db.query(query, [name], (err, result) => {
    if (err) {
      return res.status(500).send(err);
    }
    res.json({ id: result.insertId, name });
  });
});

// Απλό endpoint για έλεγχο σύνδεσης
app.get('/api/test', (req, res) => {
  res.json({ message: "Backend συνδεδεμένο επιτυχώς!" });
});

// Εκκίνηση του server
app.listen(PORT, () => {
  console.log(`Server τρέχει στο http://localhost:${PORT}`);
});