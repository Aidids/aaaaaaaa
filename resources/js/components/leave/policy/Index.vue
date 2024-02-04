<template>
    <div class="frame">
        <Card title="General Policy" icon="bi-clipboard" color="green-yellow-pin">
            <p class="mb-2">
                1. Non-annual leave must start within 1 week before today.
            </p>
            <p class="mb-2">
                2. All leave requests require at least <strong>one</strong> approver.
            </p>
            <p class="mb-2">
                3. Leave balance will be assigned after HR configures your joining date profile.
            </p>
            <p class="mb-2">
                4. For leave inquiries or balance discrepancies, please report to <strong>HR</strong>.
            </p>
        </Card>
        <Card title="Annual Leave" color="blue-red-pin">
            <p class="mb-2">
                1. You can only apply annual leave 3 days <strong>from today.</strong>
            </p>
            <p class="mb-2">
                2. Leftover leaves from the previous year will be carry forward on the 1st of January.
            </p>
            <p class="mb-2">
                3. Carry forward amount is limited based on your terms of service.
            </p>
            <p class="mb-2">
                4. Carry forward leaves will expire by July 1st.
            </p>
        </Card>
        <Card title="Offshore Leave" icon="bi-water" color="blue-blue-pin">
            <p class="mb-2">
                1. Contact or E-mail HR to request for offshore leave balance.
            </p>
            <p class="mb-2">
                2. For every 7 days spent offshore, you earn 1 day of leave.
            </p>
            <p class="mb-2">
                3. Offshore leave earned will <strong>expire</strong> by the year-end.
            </p>
        </Card>
        <Card title="Medical Leave" icon="bi-heart-pulse">
            <p class="mb-2">
                1. Medical leave entitlement is subject to your terms of service.
            </p>
            <p class="mb-2">
                2. <strong>Medical Certificate</strong> must be attached when applying for medical leave.
            </p>
        </Card>
        <Card title="Hospitalisation Leave" icon="bi-hospital" color="yellow-yellow-pin">
            <p class="mb-2">
                1. Dayang Enterprise Sdn. Bhd. provides <strong>60 days</strong> of hospitalisation leave, irrespective of terms of service.
            </p>
            <p class="mb-2">
                2. <strong>Medical Certificate</strong> must be attached when applying for hospitalisation leave.
            </p>
        </Card>
        <Card title="Maternity Leave" icon="bi-gender-female" color="white-red-pin">
            <p class="mb-2">
                1. Employees have the option to work until the day of childbirth or start maternity leave up to 30 days before their expected confinement.
            </p>
            <p class="mb-2">
                2. Female employees are entitled to <strong>98 days</strong> of paid maternity leave for <strong>each</strong> confinement.
            </p>
            <p class="mb-2">
                3. <strong>Birth Certificate</strong> must be attached when applying for maternity leave.
            </p>
        </Card>
        <Card title="Paternity Leave" icon="bi-gender-male" color="white-blue-pin">
            <p class="mb-2">
                1. Male employees are entitled to <strong>7 days</strong> of paid paternity leave for <strong>each</strong> confinement.
            </p>
            <p class="mb-2">
                2. <strong>Birth Certificate</strong> must be attached when applying for paternity leave.
            </p>
        </Card>
        <Card title="Emergency Leave" icon="bi-exclamation-diamond" color="pink-yellow-pin">
            <p class="mb-2">
                1. Emergency leave applications will deduct from your annual leave balance.
            </p>
            <p class="mb-2">
                2. If your annual leave balance is depleted, it will be considered as unpaid leave.
            </p>
        </Card>
        <Card title="Unpaid Leave" icon="bi-cash-coin" color="pink-red-pin">
            <p class="mb-2">
                1. Unpaid leave is not permitted with an active annual leave balance.
            </p>
        </Card>
    </div>
</template>

<script>
import Card from "./Card.vue";
export default {
    components: {Card}
}
</script>
