import {ref} from 'vue';
import {Channel} from "laravel-echo";

const publicChannel = ref<Channel | null>(null);

export function useSystemChannel() {
    const setChannel = (channel: Channel) => {
        publicChannel.value = channel;
    };

    return {
        publicChannel: publicChannel,
        setChannel
    };
}
