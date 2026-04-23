import { Link, usePage } from '@inertiajs/react';

export default function ResponsiveNavLink({
    active = false,
    className = '',
    children,
    ...props
}) {
    const { municipal_config } = usePage().props;
    const primaryColor = municipal_config?.primary_color || '#6366f1';

    return (
        <Link
            {...props}
            className={`flex w-full items-start border-l-4 py-2 pe-4 ps-3 ${
                active
                    ? 'focus:bg-indigo-100 focus:text-indigo-800 dark:bg-indigo-900/50 dark:focus:bg-indigo-900 dark:focus:text-indigo-200'
                    : 'border-transparent text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800 focus:border-gray-300 focus:bg-gray-50 focus:text-gray-800 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-200 dark:focus:border-gray-600 dark:focus:bg-gray-700 dark:focus:text-gray-200'
            } text-base font-medium transition duration-150 ease-in-out focus:outline-none ${className}`}
            style={active ? { 
                borderLeftColor: primaryColor, 
                color: primaryColor,
                backgroundColor: `${primaryColor}15`
            } : {}}
        >
            {children}
        </Link>
    );
}
