<template>
    <div>
        <ul>
            <li>
                Achats
                <ul>
                    <li>
                        <RouterLink to="supplier-show-chart">
                            Fournisseur
                        </RouterLink>
                    </li>
                    <li>
                        <RouterLink to="/component/show">
                            Composants
                        </RouterLink>
                    </li>
                </ul>
            </li>
            <li>
                Ventes
                <ul>
                    <li>
                        <RouterLink to="/customer/show">
                            Client
                        </RouterLink>
                    </li>
                    <li>
                        <RouterLink to="/product/show">
                            Produits
                        </RouterLink>
                    </li>
                </ul>
            </li>
            <li>
                Direction
                <ul>
                    <li>
                        <RouterLink to="company-show">
                            Compagnie
                        </RouterLink>
                    </li>
                </ul>
            </li>
            <li>
                Projet
                <ul>
                    <li>
                        <RouterLink to="/product/show">
                            Produits
                        </RouterLink>
                    </li>
                </ul>
            </li>
            <li>
                RH
                <ul>
                    <li>
                        <RouterLink to="/employee/show">
                            Employées
                        </RouterLink>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</template>
