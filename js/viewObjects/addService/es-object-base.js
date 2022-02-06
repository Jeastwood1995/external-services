class EsObjectBase {
    constructor() {
        this._ajaxConnector = new ServiceConnection();
    }

    get showerLoaderHtml() {
        return <div className="loader-overlay">
            <div className="loader-overlay-content">
                <p className="loader-message">Loading...</p>
                <span className="loader" />
            </div>
        </div>;
    }
}