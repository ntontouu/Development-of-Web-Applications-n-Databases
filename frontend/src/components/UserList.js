import React, { useState, useEffect } from 'react';

const UserList = () => {
  const [users, setUsers] = useState([]);

  useEffect(() => {
    // Κάνουμε αίτημα στο backend για να πάρουμε τους χρήστες
    fetch('http://localhost:5000/users')
      .then(response => response.json())
      .then(data => setUsers(data))
      .catch(error => console.error('Σφάλμα κατά τη λήψη χρηστών:', error));
  }, []);

  return (
    <div>
      <h2>Λίστα Χρηστών</h2>
      {users.length === 0 ? (
        <p>Δεν βρέθηκαν χρήστες</p>
      ) : (
        <ul>
          {users.map(user => (
            <li key={user.id}>{user.name}</li>
          ))}
        </ul>
      )}
    </div>
  );
};

export default UserList;