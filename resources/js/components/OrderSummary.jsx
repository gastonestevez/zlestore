import React from "react";

export const OrderSummary = ({order}) => {
    return (
        <>
            <div className="uk-overflow-auto">
                <table className="uk-table uk-table-striped uk-table-hover">
                    <thead>
                        <tr>
                            <th>N° Orden</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{order.id}</td>
                            <td>{order.date_created}</td>
                            <td>{order.status}</td>
                            <td>$ {order.total}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </>
    );
};
