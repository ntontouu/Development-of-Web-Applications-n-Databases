import React, { useState } from "react";

function UserForm({ onAddUser }) {
  const [name, setName] = useState("");
  const [error, setError] = useState("");

  const handleChange = (event) => {
    setName(event.target.value);
  };

  const handleSubmit = (event) => {
    event.preventDefault();
    if (name.trim() === "") {
      setError("Το όνομα δεν μπορεί να είναι κενό!");
    } else {
      setError("");
      onAddUser(name);
      setName("");
    }
  };

  return (
    <div>
      <form onSubmit={handleSubmit}>
        <input
          type="text"
          placeholder="Πληκτρολόγησε το όνομά σου"
          value={name}
          onChange={handleChange}
        />
        <button type="submit">Προσθήκη Χρήστη</button>
      </form>
      {error && <p style={{ color: "red" }}>{error}</p>}
    </div>
  );
}

export default UserForm;