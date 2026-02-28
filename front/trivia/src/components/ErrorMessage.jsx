function ErrorMessage({ message, onDismiss }) {
    return (
        <div className="error-bar">
            <span>Error: {message}</span>
            {onDismiss && (
                <button className="error-dismiss" onClick={onDismiss}>✕</button>
            )}
        </div>
    );
}

export default ErrorMessage;
