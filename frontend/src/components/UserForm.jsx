import React, { useState } from 'react';

const UserForm = ({ addUser }) => {
    const [name, setName] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        if (name.trim() === '') {
            alert('Το όνομα είναι υποχρεωτικό.');
            return;
        }
        addUser(name);
        setName('');
    };

    return (
        <div className="my-4">
            <form onSubmit={handleSubmit}>
                <input
                    type="text"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="Όνομα Χρήστη"
                    className="border p-2 rounded"
                />
                <button type="submit" className="ml-2 p-2 bg-blue-500 text-white rounded">Προσθήκη</button>
            </form>
        </div>
    );
};

export default UserForm;