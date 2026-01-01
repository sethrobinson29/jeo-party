import React from 'react';

function ErrorMessage({ message }) {
    return (
        <div className="bg-red-600 text-white p-4 text-center">
            <p className="font-semibold">Error: {message}</p>
        </div>
    );
}

export default ErrorMessage;