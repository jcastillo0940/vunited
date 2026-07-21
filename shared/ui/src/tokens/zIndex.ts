// El config original no tokenizaba z-index (usaba z-40/z-50 literales en
// MainNavbar/TopTicker). Se preservan esos mismos valores y se añaden los
// que faltan para los componentes nuevos (Modal, Drawer, Toast).
export const zIndex = {
    navbar: 40,
    ticker: 50,
    dropdown: 60,
    drawer: 80,
    modal: 100,
    toast: 110,
} as const;
