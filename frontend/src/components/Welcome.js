import React, { useState } from "react";

function Welcome() {
  const [name, setName] = useState(""); // Κρατάμε το όνομα που θα εισαχθεί

  const handleChange = (event) => {
    setName(event.target.value); // Αποθηκεύουμε την είσοδο του χρήστη
  };

  const handleClick = () => {
    alert(`Γεια σου, ${name}!`); // Εμφάνιση μηνύματος
  };

  return (
    <div>
      <h1>Καλώς ήρθες στο React!</h1>
      <input type="text" placeholder="Πληκτρολόγησε το όνομά σου" onChange={handleChange} />
      <button onClick={handleClick}>Υποβολή</button>
    </div>
  );
}

export default Welcome; 