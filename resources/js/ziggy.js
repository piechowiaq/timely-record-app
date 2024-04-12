const Ziggy = {
    url: "https://timelyrecord.com",
    port: null,
    defaults: {},
    routes: {
        "debugbar.openhandler": {
            uri: "_debugbar/open",
            methods: ["GET", "HEAD"],
        },
        "debugbar.clockwork": {
            uri: "_debugbar/clockwork/{id}",
            methods: ["GET", "HEAD"],
            parameters: ["id"],
        },
        "debugbar.assets.css": {
            uri: "_debugbar/assets/stylesheets",
            methods: ["GET", "HEAD"],
        },
        "debugbar.assets.js": {
            uri: "_debugbar/assets/javascript",
            methods: ["GET", "HEAD"],
        },
        "debugbar.cache.delete": {
            uri: "_debugbar/cache/{key}/{tags?}",
            methods: ["DELETE"],
            parameters: ["key", "tags"],
        },
        "sanctum.csrf-cookie": {
            uri: "sanctum/csrf-cookie",
            methods: ["GET", "HEAD"],
        },
        "ignition.healthCheck": {
            uri: "_ignition/health-check",
            methods: ["GET", "HEAD"],
        },
        "ignition.executeSolution": {
            uri: "_ignition/execute-solution",
            methods: ["POST"],
        },
        "ignition.updateConfig": {
            uri: "_ignition/update-config",
            methods: ["POST"],
        },
        test: { uri: "test", methods: ["GET", "HEAD"] },
        welcome: { uri: "/", methods: ["GET", "HEAD"] },
        "profile.edit": { uri: "profile", methods: ["GET", "HEAD"] },
        "profile.update": { uri: "profile", methods: ["PATCH"] },
        "profile.destroy": { uri: "profile", methods: ["DELETE"] },
        "projects.dashboard": { uri: "dashboard", methods: ["GET", "HEAD"] },
        "users.index": { uri: "users", methods: ["GET", "HEAD"] },
        "users.create": { uri: "users/create", methods: ["GET", "HEAD"] },
        "users.store": { uri: "users", methods: ["POST"] },
        "users.show": {
            uri: "users/{user}",
            methods: ["GET", "HEAD"],
            parameters: ["user"],
        },
        "users.edit": {
            uri: "users/{user}/edit",
            methods: ["GET", "HEAD"],
            parameters: ["user"],
            bindings: { user: "id" },
        },
        "users.update": {
            uri: "users/{user}",
            methods: ["PUT", "PATCH"],
            parameters: ["user"],
            bindings: { user: "id" },
        },
        "users.destroy": {
            uri: "users/{user}",
            methods: ["DELETE"],
            parameters: ["user"],
            bindings: { user: "id" },
        },
        "registries.index": { uri: "registries", methods: ["GET", "HEAD"] },
        "registries.create": {
            uri: "registries/create",
            methods: ["GET", "HEAD"],
        },
        "registries.store": { uri: "registries", methods: ["POST"] },
        "registries.show": {
            uri: "registries/{registry}",
            methods: ["GET", "HEAD"],
            parameters: ["registry"],
        },
        "registries.edit": {
            uri: "registries/{registry}/edit",
            methods: ["GET", "HEAD"],
            parameters: ["registry"],
            bindings: { registry: "id" },
        },
        "registries.update": {
            uri: "registries/{registry}",
            methods: ["PUT", "PATCH"],
            parameters: ["registry"],
            bindings: { registry: "id" },
        },
        "registries.destroy": {
            uri: "registries/{registry}",
            methods: ["DELETE"],
            parameters: ["registry"],
            bindings: { registry: "id" },
        },
        "workspaces.index": { uri: "workspaces", methods: ["GET", "HEAD"] },
        "workspaces.create": {
            uri: "workspaces/create",
            methods: ["GET", "HEAD"],
        },
        "workspaces.store": { uri: "workspaces", methods: ["POST"] },
        "workspaces.show": {
            uri: "workspaces/{workspace}",
            methods: ["GET", "HEAD"],
            parameters: ["workspace"],
        },
        "workspaces.edit": {
            uri: "workspaces/{workspace}/edit",
            methods: ["GET", "HEAD"],
            parameters: ["workspace"],
            bindings: { workspace: "id" },
        },
        "workspaces.update": {
            uri: "workspaces/{workspace}",
            methods: ["PUT", "PATCH"],
            parameters: ["workspace"],
            bindings: { workspace: "id" },
        },
        "workspaces.destroy": {
            uri: "workspaces/{workspace}",
            methods: ["DELETE"],
            parameters: ["workspace"],
            bindings: { workspace: "id" },
        },
        "workspaces.index-registries": {
            uri: "workspaces/{workspace}/index-registries",
            methods: ["GET", "HEAD"],
            parameters: ["workspace"],
            bindings: { workspace: "id" },
        },
        "workspaces.sync-registries": {
            uri: "workspaces/{workspace}/sync-registries",
            methods: ["PUT"],
            parameters: ["workspace"],
            bindings: { workspace: "id" },
        },
        "workspaces.dashboard": {
            uri: "workspaces/{workspace}/dashboard",
            methods: ["GET", "HEAD"],
            parameters: ["workspace"],
            bindings: { workspace: "id" },
        },
        "workspaces.registries.index": {
            uri: "workspaces/{workspace}/registries",
            methods: ["GET", "HEAD"],
            parameters: ["workspace"],
            bindings: { workspace: "id" },
        },
        "workspaces.registries.show": {
            uri: "workspaces/{workspace}/registries/{registry}",
            methods: ["GET", "HEAD"],
            parameters: ["workspace", "registry"],
        },
        "workspace.registry.reports.create": {
            uri: "projects/{project}/workspaces/{workspace}/registries/reports/create",
            methods: ["GET", "HEAD"],
            parameters: ["project", "workspace"],
            bindings: { project: "id", workspace: "id" },
        },
        "workspace.registry.reports.edit": {
            uri: "projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}/edit",
            methods: ["GET", "HEAD"],
            parameters: ["project", "workspace", "registry", "report"],
            bindings: {
                project: "id",
                workspace: "id",
                registry: "id",
                report: "id",
            },
        },
        "workspace.registry.reports.update": {
            uri: "projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}",
            methods: ["PATCH"],
            parameters: ["project", "workspace", "registry", "report"],
            bindings: {
                project: "id",
                workspace: "id",
                registry: "id",
                report: "id",
            },
        },
        "workspace.registry.reports.destroy": {
            uri: "projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}",
            methods: ["DELETE"],
            parameters: ["project", "workspace", "registry", "report"],
            bindings: {
                project: "id",
                workspace: "id",
                registry: "id",
                report: "id",
            },
        },
        "workspace.registry.reports.store": {
            uri: "projects/{project}/workspaces/{workspace}/registries/reports",
            methods: ["POST"],
            parameters: ["project", "workspace"],
            bindings: { project: "id", workspace: "id" },
        },
        "workspace.registry.reports.show": {
            uri: "projects/{project}/workspaces/{workspace}/registries/{registry}/reports/{report}",
            methods: ["GET", "HEAD"],
            parameters: ["project", "workspace", "registry", "report"],
            bindings: {
                project: "id",
                workspace: "id",
                registry: "id",
                report: "id",
            },
        },
        "user.registration": {
            uri: "user-registration/{token}",
            methods: ["GET", "HEAD"],
            parameters: ["token"],
        },
        register: { uri: "register", methods: ["GET", "HEAD"] },
        login: { uri: "login", methods: ["GET", "HEAD"] },
        "password.request": {
            uri: "forgot-password",
            methods: ["GET", "HEAD"],
        },
        "password.email": { uri: "forgot-password", methods: ["POST"] },
        "password.reset": {
            uri: "reset-password/{token}",
            methods: ["GET", "HEAD"],
            parameters: ["token"],
        },
        "password.store": { uri: "reset-password", methods: ["POST"] },
        "verification.notice": {
            uri: "verify-email",
            methods: ["GET", "HEAD"],
        },
        "verification.verify": {
            uri: "verify-email/{id}/{hash}",
            methods: ["GET", "HEAD"],
            parameters: ["id", "hash"],
        },
        "registration.send": {
            uri: "email/registration-notification/{user}",
            methods: ["POST"],
            parameters: ["user"],
            bindings: { user: "id" },
        },
        "verification.send": {
            uri: "email/verification-notification",
            methods: ["POST"],
        },
        "password.confirm": {
            uri: "confirm-password",
            methods: ["GET", "HEAD"],
        },
        "password.update": { uri: "password", methods: ["PUT"] },
        logout: { uri: "logout", methods: ["POST"] },
    },
};

if (typeof window !== "undefined" && typeof window.Ziggy !== "undefined") {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
