import { createContext, useContext } from 'react';

export const LayoutContext = createContext({ settings: {} });

export function useLayoutSettings() {
    return useContext(LayoutContext).settings;
}
