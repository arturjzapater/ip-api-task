import React from 'react';
import Spinner from './Spinner';

const Button = ({ text, spinner, onClick }) => (
    <button id="button" onClick={onClick}>
        {text}
        <Spinner show={spinner} />
    </button>
);

export default Button;
