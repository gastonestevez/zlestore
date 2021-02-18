import React, { useState } from 'react'

export const Warehouse = ({warehouse, product, search}) => {
    const [stock, setStock] = useState(0)

    const searchStock = () => (search(
        warehouse.get_products,
        product.product_id
    ))

    const handleOnChange = (e) => {
        const { value } = e.currentTarget
        if(value && searchStock() - value >= 0) {
            setStock(value)
        }
    }

    return (
        <div
            key={`${product.id}-p-${warehouse.id}-w`}
            className="uk-margin"
        >
            <label htmlFor="stock" className="uk-form-label">
                Depósito: {warehouse.name} @ {warehouse.address} /
                Disponibles:{" "}
                {searchStock()}
            </label>
            <div
                key={`${product.id}-${warehouse.id}-increment`}
                className="uk-form-controls"
            >
                <input
                    type="number"
                    className="uk-input uk-form-width-small"
                    min="0"
                    value={stock}
                    onChange={handleOnChange}
                />
            </div>
        </div>
    )
}

export default Warehouse