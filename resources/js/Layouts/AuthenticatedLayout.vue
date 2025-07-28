<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="border-b border-gray-100 bg-white">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('management.dashboard')" class="flex items-center">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                    <span class="ml-3 text-2xl font-bold text-gray-800">Personel Bilgi Sistemi</span> </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex">
                                <NavLink :href="route('management.dashboard')"
                                         :active="route().current('management.dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex">
                                <NavLink :href="route('management.users.index')"
                                         :active="route().current('management.users.index')"
                                >
                                    Personeller
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex">
                                <NavLink :href="route('management.units.index')"
                                         :active="route().current('management.units.index')"
                                >
                                    Birimler
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex">
                                <NavLink :href="route('management.leaves.index')"
                                         :active="route().current('management.leaves.index')"
                                >
                                    İzinler
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex">
                                <NavLink :href="route('management.cards.index')"
                                         :active="route().current('management.cards.index')"
                                >
                                    Kartlar
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex">
                                <NavLink :href="route('management.cars.index')"
                                         :active="route().current('management.cars.index')"
                                >
                                    Arabalar
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex">
                                <NavLink :href="route('management.announcements.index')"
                                         :active="route().current('management.announcements.index')"
                                >
                                    Duyurular
                                </NavLink>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-6 sm:flex">
                                <NavLink :href="route('management.salaries.index')"
                                         :active="route().current('management.salaries.index')"
                                >
                                    Maaşlar
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:-my-px sm:ms-auto sm:flex sm:items-center">
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Çıkış Yap
                            </Link>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('management.dashboard')"
                            :active="route().current('management.dashboard')"
                        >
                            Gösterge Paneli
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('management.users.index')"
                            :active="route().current('management.users.index')"
                        >
                            Personeller
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('management.units.index')"
                            :active="route().current('management.units.index')"
                        >
                            Birimler
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('management.leaves.index')"
                            :active="route().current('management.leaves.index')"
                        >
                            İzinler
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('management.cards.index')"
                            :active="route().current('management.cards.index')"
                        >
                            Kartlar
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('management.cars.index')"
                            :active="route().current('management.cars.index')"
                        >
                            Arabalar
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('management.announcements.index')"
                            :active="route().current('management.announcements.index')"
                        >
                            Duyurular
                        </ResponsiveNavLink>

                        <ResponsiveNavLink
                            :href="route('management.salaries.index')"
                            :active="route().current('management.salaries.index')"
                        >
                            Maaşlar
                        </ResponsiveNavLink>
                    </div>

                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Çıkış Yap
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
