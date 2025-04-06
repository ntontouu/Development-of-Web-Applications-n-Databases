import React from 'react';
import UserList from './components/UserList';
import AddUser from './components/AddUser';

const App = () => {
  return (
    <div>
      <h1>React - Node.js Σύνδεση</h1>
      <AddUser />
      <UserList />
    </div>
  );
};

export default App;
