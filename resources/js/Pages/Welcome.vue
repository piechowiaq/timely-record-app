<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const projectId = usePage().props.projectId;

const logout = () => {
    axios
        .post("/logout")
        .then(() => {
            window.location.href = "/";
        })
        .catch((error) => {
            console.error("Logout failed:", error);
        });
};
</script>

<template>
    <Head title="Welcome" />

    <div
        class="relative min-h-screen bg-gray-100 bg-center selection:bg-gray-500 selection:text-white dark:bg-gray-900 sm:flex sm:items-center sm:justify-center"
    >
        <div
            v-if="canLogin"
            class="p-6 text-right sm:fixed sm:right-0 sm:top-0"
        >
            <Link
                v-if="projectId"
                :href="route('projects.dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 focus:rounded-sm focus:outline focus:outline-2 focus:outline-gray-500 dark:text-gray-400 dark:hover:text-white"
                >Dashboard
            </Link>
            <button
                v-if="projectId"
                @click="logout"
                class="ml-4 font-semibold text-gray-600 hover:text-gray-900 focus:rounded-sm focus:outline focus:outline-2 focus:outline-gray-500 dark:text-gray-400 dark:hover:text-white"
            >
                Log Out
            </button>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 focus:rounded-sm focus:outline focus:outline-2 focus:outline-gray-500 dark:text-gray-400 dark:hover:text-white"
                    >Log in
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ml-4 font-semibold text-gray-600 hover:text-gray-900 focus:rounded-sm focus:outline focus:outline-2 focus:outline-gray-500 dark:text-gray-400 dark:hover:text-white"
                    >Register
                </Link>
            </template>
        </div>

        <div class="mx-auto max-w-7xl p-6 lg:p-8">
            <div class="px-10">
                <div
                    class="flex flex-col items-center justify-center md:flex-row md:justify-start"
                >
                    <ApplicationLogo class="h-20 w-20" />
                    <p
                        class="ml-2 whitespace-nowrap text-2xl font-bold tracking-widest text-gray-600"
                    >
                        <span class="text-cyan-600">TIMELY</span> RECORD
                    </p>
                </div>
                <div class="mt-8 text-xl dark:text-white">
                    Welcome to your Timely Record application!
                </div>
                <div class="mt-6 text-justify text-gray-500">
                    "Timely Record - Zapis na czas" to aplikacja przeznaczona do
                    zarządzania dokumentacją charakterystyczną dla obiektów
                    hotelowych. Zapewnia monitorowanie nad terminową
                    aktualizacją istotnych dokumentów w oparciu o przepisy prawa
                    oraz standardy branży hotelowej. Aplikacja pozwala również
                    dostosować kontrolę dodatkowych dokumentów określonych
                    indywidualnie przez daną sieć hotelową lub firmę.
                </div>
            </div>

            <div class="mt-8">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:gap-8">
                    <div
                        class="duration-250 flex rounded-lg bg-white from-gray-700/50 via-transparent p-6 shadow-2xl shadow-gray-500/20 transition-all focus:outline focus:outline-2 focus:outline-gray-500 dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:shadow-none dark:ring-1 dark:ring-inset dark:ring-white/5"
                    >
                        <div>
                            <div
                                class="flex h-16 w-16 items-center justify-center bg-gray-50 dark:bg-red-800/20"
                            >
                                <i
                                    class="fa-solid fa-building-shield text-cyan-600"
                                ></i>
                            </div>

                            <h2
                                class="mt-6 text-xl font-semibold text-gray-900 dark:text-white"
                            >
                                Workspaces
                            </h2>

                            <p
                                class="mt-4 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                            >
                                Umożliwiają elastyczne dostosowanie dokumentacji
                                indywidualnie do poszczególnych obiektów w
                                ramach jednego projektu.
                            </p>
                        </div>
                    </div>

                    <div
                        class="duration-250 flex rounded-lg bg-white from-gray-700/50 via-transparent p-6 shadow-2xl shadow-gray-500/20 transition-all focus:outline focus:outline-2 focus:outline-gray-500 dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:shadow-none dark:ring-1 dark:ring-inset dark:ring-white/5"
                    >
                        <div>
                            <div
                                class="flex h-16 w-16 items-center justify-center bg-gray-50 dark:bg-red-800/20"
                            >
                                <i
                                    class="fa-solid fa-box-archive text-cyan-600"
                                ></i>
                            </div>

                            <h2
                                class="mt-6 text-xl font-semibold text-gray-900 dark:text-white"
                            >
                                Registries
                            </h2>

                            <p
                                class="mt-4 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                            >
                                To opracowane rejestry wymaganej prawnie lub
                                zalecanej dokumentacji objęte określonymi
                                terminami ważności, wraz z ich opisem.
                            </p>
                        </div>
                    </div>

                    <div
                        class="duration-250 flex rounded-lg bg-white from-gray-700/50 via-transparent p-6 shadow-2xl shadow-gray-500/20 transition-all focus:outline focus:outline-2 focus:outline-gray-500 dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:shadow-none dark:ring-1 dark:ring-inset dark:ring-white/5"
                    >
                        <div>
                            <div
                                class="flex h-16 w-16 items-center justify-center bg-gray-50 dark:bg-red-800/20"
                            >
                                <i class="fa-solid fa-users text-cyan-600"></i>
                            </div>

                            <h2
                                class="mt-6 text-xl font-semibold text-gray-900 dark:text-white"
                            >
                                Users
                            </h2>

                            <p
                                class="mt-4 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                            >
                                Narzędzie posiada elastyczny system nadawania
                                uprawnień, umożliwiający precyzyjne dostosowanie
                                dostępu każdego użytkownika do przypisanych
                                obiektów.
                            </p>
                        </div>
                    </div>

                    <div
                        class="duration-250 flex rounded-lg bg-white from-gray-700/50 via-transparent p-6 shadow-2xl shadow-gray-500/20 transition-all focus:outline focus:outline-2 focus:outline-gray-500 dark:bg-gray-800/50 dark:bg-gradient-to-bl dark:shadow-none dark:ring-1 dark:ring-inset dark:ring-white/5"
                    >
                        <div>
                            <div
                                class="flex h-16 w-16 items-center justify-center bg-gray-50 dark:bg-red-800/20"
                            >
                                <i class="fa-solid fa-book text-cyan-600"></i>
                            </div>

                            <ul
                                class="mt-4 text-sm leading-relaxed text-gray-500 dark:text-gray-400"
                            >
                                <li>Dokumentacja PPOŻ</li>
                                <li>Dokumentacja HACCP</li>
                                <li>Dokumentacja BHP</li>
                                <li>
                                    Dokumentacja związana z kategoryzacją
                                    obiektu
                                </li>
                                <li>Dokumentacja związana z bezpieczeństwem</li>
                                <li>Dokumentacja techniczna</li>
                                <li>and more ...</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="mt-8 flex justify-center px-6 sm:items-center sm:justify-between"
            >
                <div
                    class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-left"
                >
                    <div class="flex items-center gap-4">
                        <div>
                            <a
                                href="https://linkedin.com/company/timely-record/"
                                target="_blank"
                            >
                                <svg
                                    class="fill-gray-500 hover:fill-cyan-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M20.47 2H3.53a1.45 1.45 0 0 0-1.47 1.43v17.14A1.45 1.45 0 0 0 3.53 22h16.94a1.45 1.45 0 0 0 1.47-1.43V3.43A1.45 1.45 0 0 0 20.47 2ZM8.09 18.74h-3v-9h3ZM6.59 8.48a1.56 1.56 0 1 1 0-3.12a1.57 1.57 0 1 1 0 3.12Zm12.32 10.26h-3v-4.83c0-1.21-.43-2-1.52-2A1.65 1.65 0 0 0 12.85 13a2 2 0 0 0-.1.73v5h-3v-9h3V11a3 3 0 0 1 2.71-1.5c2 0 3.45 1.29 3.45 4.06Z"
                                    />
                                </svg>
                            </a>
                        </div>
                        <div class="ml-2">
                            <a
                                href="https://facebook.com/people/Timely-Record/61550085031784/"
                                target="_blank"
                            >
                                <svg
                                    class="fill-gray-500 hover:fill-cyan-600"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        id="feFacebook2"
                                        d="M15.725 22v-7.745h2.6l.389-3.018h-2.99V9.31c0-.874.243-1.47 1.497-1.47h1.598v-2.7a21.391 21.391 0 0 0-2.33-.12c-2.304 0-3.881 1.407-3.881 3.99v2.227H10v3.018h2.607V22H3.104C2.494 22 2 21.506 2 20.896V3.104C2 2.494 2.494 2 3.104 2h17.792C21.506 2 22 2.494 22 3.104v17.792c0 .61-.494 1.104-1.104 1.104h-5.171Z"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div
                    class="ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:ml-0 sm:text-right"
                >
                    <p
                        class="rounded-md text-xs text-gray-600 hover:text-cyan-700"
                    >
                        contact@timelyrecord.com
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
