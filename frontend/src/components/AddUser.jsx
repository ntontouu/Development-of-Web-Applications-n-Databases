import React, { useState } from 'react';

const AddUser = () => {
  const [name, setName] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();

    fetch('http://localhost:5000/users', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ name }),
    })
      .then((response) => response.json())
      .then((data) => {
        console.log('Χρήστης προστέθηκε:', data);
        setName(''); // Επαναφορά της φόρμας
      })
      .catch((error) => console.error('Σφάλμα κατά την προσθήκη χρήστη:', error));
  };

  return (
    <div>
      <h2>Προσθήκη Χρήστη</h2>
      <form onSubmit={handleSubmit}>
        <input
          type="text"
          value={name}
          onChange={(e) => setName(e.target.value)}
          placeholder="Όνομα"
          required
        />
        <button type="submit">Προσθήκη Χρήστη</button>
      </form>
    </div>
  );
};

export default AddUser;