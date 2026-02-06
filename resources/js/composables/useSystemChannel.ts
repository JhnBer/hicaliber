import type {Channel} from "laravel-echo";
import {ref} from 'vue';

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
