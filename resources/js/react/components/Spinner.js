import React from 'react';

const Spinner = ({ show }) => {
    if (!show) return null;
    
    return (
        <span id="loading">
            <i className="fa fa-spin fa-spinner"></i>
        </span>
    );
};

export default Spinner;
